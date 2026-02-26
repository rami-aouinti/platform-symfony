<?php

declare(strict_types=1);

namespace App\Notification\Application\Service;

use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\Notification\Application\Service\Interfaces\NotificationOrchestratorInterface;
use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\Notification\Domain\Enum\NotificationType;
use App\Notification\Domain\Message\NotificationRealtimePublishMessage;
use App\User\Domain\Entity\User;

use function sprintf;

/**
 * @package App\Notification
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

readonly class NotificationOrchestrator implements NotificationOrchestratorInterface
{
    public function __construct(
        private NotificationServiceInterface $notificationService,
        private MessageServiceInterface $messageService,
    ) {
    }

    public function notifyCompanyCreated(User $owner, string $companyLegalName): void
    {
        $title = sprintf('Société "%s" créée', $companyLegalName);
        $message = sprintf('Votre société "%s" a été créée avec succès.', $companyLegalName);

        $this->createAndPublish($owner, NotificationType::COMPANY_CREATED, $title, $message);
    }

    public function notifyJobApplicationSubmitted(
        User $candidate,
        ?User $offerOwnerOrCreator,
        string $applicationId,
        string $offerId,
    ): void {
        $candidateTitle = 'Confirmation de candidature';
        $candidateMessage = sprintf(
            'Votre candidature %s pour l\'offre %s a bien été enregistrée.',
            $applicationId,
            $offerId,
        );

        $this->createAndPublish($candidate, NotificationType::JOB_APPLICATION_SUBMITTED, $candidateTitle, $candidateMessage);

        if ($offerOwnerOrCreator instanceof User && $offerOwnerOrCreator->getId() !== $candidate->getId()) {
            $ownerTitle = 'Nouvelle candidature reçue';
            $ownerMessage = sprintf(
                'Une nouvelle candidature %s a été soumise sur l\'offre %s.',
                $applicationId,
                $offerId,
            );

            $this->createAndPublish($offerOwnerOrCreator, NotificationType::JOB_APPLICATION_SUBMITTED, $ownerTitle, $ownerMessage);
        }
    }

    public function notifyJobApplicationDecided(
        User $candidate,
        JobApplicationStatus $status,
        string $applicationId,
        string $offerId,
    ): void {
        $decision = match ($status) {
            JobApplicationStatus::ACCEPTED => 'acceptée',
            JobApplicationStatus::REJECTED => 'rejetée',
            JobApplicationStatus::WITHDRAWN => 'retirée',
            default => $status->value,
        };

        $title = 'Mise à jour de candidature';
        $message = sprintf(
            'Votre candidature %s pour l\'offre %s a été %s.',
            $applicationId,
            $offerId,
            $decision,
        );

        $this->createAndPublish($candidate, NotificationType::JOB_APPLICATION_DECIDED, $title, $message);
    }

    private function createAndPublish(User $user, NotificationType $type, string $title, string $message): void
    {
        $this->notificationService->create($user, $type->value, $title, $message);

        $this->messageService->sendMessage(new NotificationRealtimePublishMessage(
            userId: $user->getId(),
            title: $title,
            message: $message,
        ));
    }
}

