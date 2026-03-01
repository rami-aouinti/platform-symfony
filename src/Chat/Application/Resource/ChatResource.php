<?php

declare(strict_types=1);

namespace App\Chat\Application\Resource;

use App\Chat\Application\DTO\Chat\ChatMessageView;
use App\Chat\Application\DTO\Chat\ConversationCreate;
use App\Chat\Application\DTO\Chat\ConversationView;
use App\Chat\Application\Resource\Interfaces\ChatResourceInterface;
use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\Chat\Domain\Message\ChatMessageRealtimePublishMessage;
use App\Chat\Domain\Repository\Interfaces\ChatMessageRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationRepositoryInterface;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\Recruit\Domain\Entity\JobApplication;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function array_filter;
use function array_map;
use function array_values;

/**
 * @package App\Chat\Application\Resource
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ChatResource implements ChatResourceInterface
{
    public function __construct(
        private readonly ConversationRepositoryInterface $conversationRepository,
        private readonly ConversationParticipantRepositoryInterface $participantRepository,
        private readonly ChatMessageRepositoryInterface $messageRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly MessageServiceInterface $messageService,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function createConversation(ConversationCreate $dto): ConversationView
    {
        $jobApplication = $dto->getJobApplication();

        if (!$jobApplication instanceof JobApplication) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Job application is required.');
        }

        if ($jobApplication->getStatus() !== JobApplicationStatus::ACCEPTED) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Conversation is available only for accepted applications.');
        }

        $existing = $this->conversationRepository->findOneBy([
            'jobApplication' => $jobApplication,
        ]);
        if ($existing instanceof Conversation) {
            return $this->toView($existing);
        }

        $candidate = $jobApplication->getCandidate();
        $offerOwner = $jobApplication->getJobOffer()?->getCompany()?->getOwner() ?? $jobApplication->getJobOffer()?->getCreatedBy();

        if (!$candidate instanceof User || !$offerOwner instanceof User) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Conversation participants are missing for this application.');
        }

        $conversation = (new Conversation())
            ->setJobApplication($jobApplication);

        $candidateParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($candidate);

        $ownerParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($offerOwner);

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

        return $this->toMessageViews($this->messageRepository->findByConversationId($conversation->getId()));
    }

    public function createMessage(string $conversationId, string $content): ChatMessageView
    {
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_POST);

        $message = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($this->getCurrentUser())
            ->setContent($content);

        $this->messageRepository->save($message);

        $this->messageService->sendMessage(new ChatMessageRealtimePublishMessage($message->getId()));

        return new ChatMessageView($message);
    }

    public function updateMessage(string $messageId, string $content): ChatMessageView
    {
        $message = $this->findMessage($messageId);

        if (!$this->authorizationChecker->isGranted(Permission::CHAT_EDIT->value, $message)) {
            throw new AccessDeniedHttpException('Only the author can edit this message.');
        }

        $message->setContent($content);

        $this->messageRepository->save($message);

        return new ChatMessageView($message);
    }

    public function deleteMessage(string $messageId): void
    {
        $message = $this->findMessage($messageId);

        if (!$this->authorizationChecker->isGranted(Permission::CHAT_DELETE->value, $message)) {
            throw new AccessDeniedHttpException('Only the author can delete this message.');
        }

        $this->messageRepository->remove($message);
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

    private function findAllowedConversation(string $conversationId, Permission $permission): Conversation
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation instanceof Conversation || $conversation->getJobApplication() === null) {
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

        if (!$conversation instanceof Conversation || $conversation->getJobApplication() === null) {
            return null;
        }

        if (!$this->authorizationChecker->isGranted(Permission::CHAT_VIEW->value, $conversation)) {
            return null;
        }

        return $this->toView($conversation);
    }

    private function toView(Conversation $conversation): ConversationView
    {
        return new ConversationView($conversation, $this->toMessageViews($this->messageRepository->findByConversationId($conversation->getId())));
    }

    /**
     * @param ChatMessage[] $messages
     *
     * @return ChatMessageView[]
     */
    private function toMessageViews(array $messages): array
    {
        return array_map(
            static fn (ChatMessage $message): ChatMessageView => new ChatMessageView($message),
            $messages,
        );
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
