<?php

declare(strict_types=1);

namespace App\Recruit\Transport\MessageHandler;

use App\Notification\Application\Service\Interfaces\NotificationOrchestratorInterface;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\Recruit\Domain\Message\JobApplicationDecidedMessage;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @package App\Recruit\Transport\MessageHandler
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[AsMessageHandler]
readonly class JobApplicationDecidedMessageHandler
{
    public function __construct(
        private NotificationOrchestratorInterface $notificationOrchestrator,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(JobApplicationDecidedMessage $message): void
    {
        $candidate = $this->userRepository->find($message->candidateUserId);

        if ($candidate === null) {
            return;
        }

        $status = JobApplicationStatus::tryFrom($message->status);

        if ($status === null) {
            return;
        }

        $this->notificationOrchestrator->notifyJobApplicationDecided(
            candidate: $candidate,
            status: $status,
            applicationId: $message->applicationId,
            offerId: $message->offerId,
        );
    }
}
