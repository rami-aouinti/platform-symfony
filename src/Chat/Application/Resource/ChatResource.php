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
use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function array_map;
use function in_array;

class ChatResource implements ChatResourceInterface
{
    public function __construct(
        private readonly ConversationRepositoryInterface $conversationRepository,
        private readonly ConversationParticipantRepositoryInterface $participantRepository,
        private readonly ChatMessageRepositoryInterface $messageRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly MessageServiceInterface $messageService,
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

    public function getConversation(string $conversationId): ConversationView
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation instanceof Conversation) {
            throw new NotFoundHttpException('Conversation not found.');
        }

        $this->assertCurrentUserCanAccessConversation($conversation);

        return $this->toView($conversation);
    }

    public function sendMessage(string $conversationId, string $content): ConversationView
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation instanceof Conversation) {
            throw new NotFoundHttpException('Conversation not found.');
        }

        $currentUser = $this->getCurrentUser();
        $this->assertCurrentUserCanAccessConversation($conversation, $currentUser);

        $message = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($currentUser)
            ->setContent($content);

        $this->messageRepository->save($message);

        $this->messageService->sendMessage(new ChatMessageRealtimePublishMessage($message->getId()));

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

    private function assertCurrentUserCanAccessConversation(Conversation $conversation, ?User $currentUser = null): void
    {
        $currentUser ??= $this->getCurrentUser();
        $participantUserIds = array_map(
            static fn (ConversationParticipant $participant): string => $participant->getUser()?->getId() ?? '',
            $this->participantRepository->findByConversationId($conversation->getId()),
        );

        if (!in_array($currentUser->getId(), $participantUserIds, true)) {
            throw new AccessDeniedHttpException('You are not a participant of this conversation.');
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
