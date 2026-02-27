<?php

declare(strict_types=1);

namespace App\Recruit\Transport\MessageHandler;

use App\Notification\Application\Service\Interfaces\NotificationOrchestratorInterface;
use App\Recruit\Domain\Message\JobApplicationSubmittedMessage;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @package App\Recruit\Transport\MessageHandler
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[AsMessageHandler]
readonly class JobApplicationSubmittedMessageHandler
{
    public function __construct(
        private NotificationOrchestratorInterface $notificationOrchestrator,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(JobApplicationSubmittedMessage $message): void
    {
        $candidate = $this->userRepository->find($message->candidateUserId);

        if ($candidate === null) {
            return;
        }

        $ownerOrCreator = $message->offerOwnerOrCreatorUserId !== null
            ? $this->userRepository->find($message->offerOwnerOrCreatorUserId)
            : null;

        $this->notificationOrchestrator->notifyJobApplicationSubmitted(
            candidate: $candidate,
            offerOwnerOrCreator: $ownerOrCreator,
            applicationId: $message->applicationId,
            offerId: $message->offerId,
        );
    }
}
