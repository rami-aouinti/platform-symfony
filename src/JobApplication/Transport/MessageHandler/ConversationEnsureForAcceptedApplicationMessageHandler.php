<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\MessageHandler;

use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationRepositoryInterface;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\JobApplication\Domain\Message\ConversationEnsureForAcceptedApplicationMessage;
use App\JobApplication\Infrastructure\Repository\JobApplicationRepository;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ConversationEnsureForAcceptedApplicationMessageHandler
{
    public function __construct(
        private JobApplicationRepository $jobApplicationRepository,
        private ConversationRepositoryInterface $conversationRepository,
        private ConversationParticipantRepositoryInterface $participantRepository,
    ) {
    }

    public function __invoke(ConversationEnsureForAcceptedApplicationMessage $message): void
    {
        $application = $this->jobApplicationRepository->find($message->applicationId);

        if ($application === null || $application->getStatus() !== JobApplicationStatus::ACCEPTED) {
            return;
        }

        $existing = $this->conversationRepository->findOneByJobApplicationId($application->getId());
        if ($existing instanceof Conversation) {
            return;
        }

        $candidate = $application->getCandidate();
        $offerOwner = $application->getJobOffer()?->getCompany()?->getOwner() ?? $application->getJobOffer()?->getCreatedBy();

        if (!$candidate instanceof User || !$offerOwner instanceof User) {
            return;
        }

        $conversation = (new Conversation())
            ->setJobApplication($application);

        $candidateParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($candidate);

        $ownerParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($offerOwner);

        try {
            $this->conversationRepository->save($conversation, false);
            $this->participantRepository->save($candidateParticipant, false);
            $this->participantRepository->save($ownerParticipant);
        } catch (UniqueConstraintViolationException) {
            // Idempotence guard for concurrent/reprocessed messages.
        }
    }
}
