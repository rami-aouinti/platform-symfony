<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\MessageHandler;

use App\JobApplication\Domain\Message\JobApplicationSubmittedMessage;
use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;
use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use function sprintf;

#[AsMessageHandler]
readonly class JobApplicationSubmittedMessageHandler
{
    public function __construct(
        private NotificationChannelServiceInterface $notificationChannelService,
        private NotificationServiceInterface $notificationService,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(JobApplicationSubmittedMessage $message): void
    {
        $candidate = $this->userRepository->find($message->candidateUserId);

        if ($candidate === null) {
            return;
        }

        $candidateSubject = 'Confirmation de candidature';
        $candidateContent = sprintf(
            'Votre candidature %s pour l\'offre %s a bien été enregistrée.',
            $message->applicationId,
            $message->offerId,
        );

        $this->notificationChannelService->sendEmailNotification($candidate->getEmail(), $candidateSubject, $candidateContent);
        $this->notificationChannelService->sendPushNotification($candidate->getId(), $candidateSubject, $candidateContent);
        $this->notificationService->create($candidate, 'job_application_submitted', $candidateSubject, $candidateContent);

        if ($message->offerOwnerOrCreatorUserId === null || $message->offerOwnerOrCreatorUserId === $candidate->getId()) {
            return;
        }

        $ownerOrCreator = $this->userRepository->find($message->offerOwnerOrCreatorUserId);

        if ($ownerOrCreator === null) {
            return;
        }

        $ownerSubject = 'Nouvelle candidature reçue';
        $ownerContent = sprintf(
            'Une nouvelle candidature %s a été soumise sur l\'offre %s.',
            $message->applicationId,
            $message->offerId,
        );

        $this->notificationChannelService->sendEmailNotification($ownerOrCreator->getEmail(), $ownerSubject, $ownerContent);
        $this->notificationChannelService->sendPushNotification($ownerOrCreator->getId(), $ownerSubject, $ownerContent);
        $this->notificationService->create($ownerOrCreator, 'job_application_submitted', $ownerSubject, $ownerContent);
    }
}
