<?php

declare(strict_types=1);

namespace App\Chat\Application\Resource;

use App\Chat\Application\DTO\Chat\ChatMessageView;
use App\Chat\Application\DTO\Chat\ConversationCreate;
use App\Chat\Application\DTO\Chat\ConversationView;
use App\Chat\Application\Resource\Interfaces\ChatResourceInterface;
use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\ChatMessageReaction;
use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\Chat\Domain\Message\ChatMessageRealtimePublishMessage;
use App\Chat\Domain\Repository\Interfaces\ChatMessageReactionRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ChatMessageRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationRepositoryInterface;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Throwable;

use function array_filter;
use function array_map;
use function array_values;
use function in_array;

readonly class ChatResource implements ChatResourceInterface
{
    private const array ALLOWED_REACTIONS = ['heart', 'thumbs_up', 'sad', 'surprised', 'laugh', 'fire'];

    public function __construct(
        private ConversationRepositoryInterface $conversationRepository,
        private ConversationParticipantRepositoryInterface $participantRepository,
        private ChatMessageRepositoryInterface $messageRepository,
        private ChatMessageReactionRepositoryInterface $reactionRepository,
        private UserRepositoryInterface $userRepository,
        private UserTypeIdentification $userTypeIdentification,
        private MessageServiceInterface $messageService,
        private AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function createConversation(ConversationCreate $dto, User $sender, User $receiver): ConversationView
    {
        $conversation = (new Conversation());

        $candidateParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($sender);

        $ownerParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($receiver);

        $this->conversationRepository->save($conversation, false);
        $this->participantRepository->save($candidateParticipant, false);
        $this->participantRepository->save($ownerParticipant);

        return $this->toView($conversation);
    }

    public function listConversationsForCurrentUser(): array
    {
        $currentUser = $this->getCurrentUser();
        $conversationIds = $this->participantRepository->findConversationIdsByUserId($currentUser->getId());
        $conversationIds = array_values(array_filter($conversationIds, static fn (string $id): bool => $id !== ''));

        $conversations = array_map(
            fn (string $conversationId): ?ConversationView => $this->tryBuildAllowedConversationView($conversationId),
            $conversationIds,
        );

        return array_values(array_filter($conversations, static fn (?ConversationView $view): bool => $view instanceof ConversationView));
    }

    public function getConversation(string $conversationId): ConversationView
    {
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_VIEW);
        $this->markConversationMessagesAsRead($conversation);

        return $this->toView($conversation);
    }

    public function sendMessage(string $conversationId, string $content): ConversationView
    {
        $this->createMessage($conversationId, $content);
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_VIEW);

        return $this->toView($conversation);
    }

    public function listMessages(string $conversationId): array
    {
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_VIEW);
        $this->markConversationMessagesAsRead($conversation);

        return $this->toMessageViews($this->messageRepository->findByConversationId($conversation->getId()));
    }

    public function createMessage(string $conversationId, string $content, array $attachments = []): ChatMessageView
    {
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_POST);

        $message = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($this->getCurrentUser())
            ->setContent($content)
            ->setAttachments($attachments);

        $this->messageRepository->save($message);
        $this->messageService->sendMessage(new ChatMessageRealtimePublishMessage($message->getId()));

        return new ChatMessageView($message, $this->getCurrentUser()->getId());
    }

    public function updateMessage(string $messageId, string $content, array $attachments = []): ChatMessageView
    {
        $message = $this->findMessage($messageId);

        if (!$this->authorizationChecker->isGranted(Permission::CHAT_EDIT->value, $message)) {
            throw new AccessDeniedHttpException('Only the author can edit this message.');
        }

        $message->setContent($content)->setAttachments($attachments);
        $this->messageRepository->save($message);

        return new ChatMessageView($message, $this->getCurrentUser()->getId());
    }

    public function deleteMessage(string $messageId): void
    {
        $message = $this->findMessage($messageId);

        if (!$this->authorizationChecker->isGranted(Permission::CHAT_DELETE->value, $message)) {
            throw new AccessDeniedHttpException('Only the author can delete this message.');
        }

        $this->messageRepository->remove($message);
    }

    public function addReaction(string $messageId, string $reaction): ChatMessageView
    {
        $message = $this->findMessage($messageId);
        $currentUser = $this->getCurrentUser();
        $normalized = $this->normalizeReaction($reaction);

        if ($this->reactionRepository->findOneByMessageUserReaction($message, $currentUser, $normalized) === null) {
            $entity = (new ChatMessageReaction())
                ->setMessage($message)
                ->setUser($currentUser)
                ->setReaction($normalized);
            $this->reactionRepository->save($entity);
        }

        return new ChatMessageView($message, $currentUser->getId());
    }

    public function removeReaction(string $messageId, string $reaction): ChatMessageView
    {
        $message = $this->findMessage($messageId);
        $currentUser = $this->getCurrentUser();
        $normalized = $this->normalizeReaction($reaction);
        $entity = $this->reactionRepository->findOneByMessageUserReaction($message, $currentUser, $normalized);

        if ($entity !== null) {
            $this->reactionRepository->remove($entity);
        }

        return new ChatMessageView($message, $currentUser->getId());
    }

    public function addParticipant(string $conversationId, string $userId): ConversationView
    {
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_PARTICIPANT_MANAGE);
        $user = $this->findUser($userId);

        $existingParticipant = $this->participantRepository->findOneByConversationAndUser($conversation, $user);
        if (!$existingParticipant instanceof ConversationParticipant) {
            $participant = (new ConversationParticipant())
                ->setConversation($conversation)
                ->setUser($user);

            $this->participantRepository->save($participant);
        }

        return $this->toView($conversation);
    }

    public function removeParticipant(string $conversationId, string $userId): ConversationView
    {
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_PARTICIPANT_MANAGE);
        $user = $this->findUser($userId);

        $participant = $this->participantRepository->findOneByConversationAndUser($conversation, $user);

        if (!$participant instanceof ConversationParticipant) {
            throw new NotFoundHttpException('Participant not found for this conversation.');
        }

        $this->participantRepository->remove($participant);

        return $this->toView($conversation);
    }

    public function listMessagesForModeration(): array
    {
        $this->assertModerationGranted();

        return $this->toMessageViews($this->messageRepository->findAllForModeration());
    }

    public function deleteMessageForModeration(string $messageId): void
    {
        $this->assertModerationGranted();

        $message = $this->findMessage($messageId);
        $this->messageRepository->remove($message);
    }

    private function normalizeReaction(string $reaction): string
    {
        $normalized = strtolower(trim($reaction));
        if (!in_array($normalized, self::ALLOWED_REACTIONS, true)) {
            throw new NotFoundHttpException('Unsupported reaction.');
        }

        return $normalized;
    }

    private function findAllowedConversation(string $conversationId, Permission $permission): Conversation
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation instanceof Conversation) {
            throw new NotFoundHttpException('Conversation not found.');
        }

        if (!$this->authorizationChecker->isGranted($permission->value, $conversation)) {
            throw new AccessDeniedHttpException('You are not allowed to access this conversation.');
        }

        return $conversation;
    }

    private function findMessage(string $messageId): ChatMessage
    {
        $message = $this->messageRepository->findById($messageId);

        if (!$message instanceof ChatMessage) {
            throw new NotFoundHttpException('Message not found.');
        }

        $conversation = $message->getConversation();

        if (!$conversation instanceof Conversation || !$this->authorizationChecker->isGranted(Permission::CHAT_VIEW->value, $conversation)) {
            throw new AccessDeniedHttpException('You are not allowed to access this message.');
        }

        return $message;
    }

    private function findUser(string $userId): User
    {
        $user = $this->userRepository->loadUserByIdentifier($userId, true);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }

    private function assertModerationGranted(): void
    {
        if (!$this->authorizationChecker->isGranted('ROLE_ADMIN') && !$this->authorizationChecker->isGranted('ROLE_ROOT')) {
            throw new AccessDeniedHttpException('You are not allowed to moderate chat messages.');
        }
    }

    private function tryBuildAllowedConversationView(string $conversationId): ?ConversationView
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation instanceof Conversation) {
            return null;
        }

        if (!$this->authorizationChecker->isGranted(Permission::CHAT_VIEW->value, $conversation)) {
            return null;
        }

        return $this->toView($conversation);
    }

    private function toView(Conversation $conversation): ConversationView
    {
        $currentUser = $this->getCurrentUser();

        return new ConversationView(
            $conversation,
            $this->toMessageViews($this->messageRepository->findByConversationId($conversation->getId())),
            $currentUser->getId(),
        );
    }

    /**
     * @param ChatMessage[] $messages
     *
     * @return ChatMessageView[]
     */
    private function toMessageViews(array $messages): array
    {
        $currentUser = $this->getCurrentUser();

        return array_map(
            static fn (ChatMessage $message): ChatMessageView => new ChatMessageView($message, $currentUser->getId()),
            $messages,
        );
    }

    private function markConversationMessagesAsRead(Conversation $conversation): void
    {
        $currentUser = $this->getCurrentUser();
        $messages = $this->messageRepository->findByConversationId($conversation->getId());
        $hasChanges = false;

        foreach ($messages as $message) {
            $sender = $message->getSender();
            if ($sender !== null && $sender->getId() !== $currentUser->getId() && $message->getReadAt() === null) {
                $message->setReadAt(new \DateTimeImmutable());
                $hasChanges = true;
            }
        }

        if ($hasChanges) {
            foreach ($messages as $message) {
                $this->messageRepository->save($message, false);
            }
            $this->messageRepository->save($messages[0], true);
        }
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
