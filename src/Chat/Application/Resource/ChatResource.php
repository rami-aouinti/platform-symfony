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
use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function array_map;

/**
 * @package App\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ChatResource implements ChatResourceInterface
{
    public function __construct(
        private readonly ConversationRepositoryInterface $conversationRepository,
        private readonly ConversationParticipantRepositoryInterface $participantRepository,
        private readonly ChatMessageRepositoryInterface $messageRepository,
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

        $existing = $this->conversationRepository->findOneBy(['jobApplication' => $jobApplication]);
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
        $conversation = $this->findAllowedConversation($conversationId, Permission::CHAT_POST);

        $message = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($this->getCurrentUser())
            ->setContent($content);

        $this->messageRepository->save($message);

        $this->messageService->sendMessage(new ChatMessageRealtimePublishMessage($message->getId()));

        return $this->toView($conversation);
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
        $messages = array_map(
            static fn (ChatMessage $message): ChatMessageView => new ChatMessageView($message),
            $this->messageRepository->findByConversationId($conversation->getId()),
        );

        return new ConversationView($conversation, $messages);
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
