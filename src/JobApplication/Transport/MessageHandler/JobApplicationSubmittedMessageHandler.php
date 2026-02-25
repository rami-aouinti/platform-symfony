<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\MessageHandler;

use App\JobApplication\Domain\Message\JobApplicationSubmittedMessage;
use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

use function sprintf;

#[AsMessageHandler]
readonly class JobApplicationSubmittedMessageHandler
{
    public function __construct(
        private NotificationChannelServiceInterface $notificationChannelService,
        private NotificationRepositoryInterface $notificationRepository,
        private UserRepository $userRepository,
        private HubInterface $hub,
    ) {
    }

    public function __invoke(JobApplicationSubmittedMessage $message): void
    {
        if ($message->reviewerId === null || $message->reviewerEmail === null) {
            return;
        }

        $reviewer = $this->userRepository->find($message->reviewerId);

        if ($reviewer === null) {
            return;
        }

        $subject = sprintf('Nouvelle candidature pour "%s"', $message->jobOfferTitle);
        $content = sprintf('Une nouvelle candidature a été soumise pour l\'offre "%s".', $message->jobOfferTitle);

        $this->notificationChannelService->sendEmailNotification($message->reviewerEmail, $subject, $content);
        $this->notificationChannelService->sendPushNotification($message->reviewerEmail, $subject, $content);

        $notification = (new Notification($reviewer))
            ->setType('job_application_submitted')
            ->setTitle($subject)
            ->setMessage($content);

        $this->notificationRepository->save($notification);

        $this->hub->publish(new Update(
            sprintf('/users/%s/notifications', $message->reviewerId),
            sprintf(
                '{"type":"job_application_submitted","jobApplicationId":"%s","jobOfferId":"%s"}',
                $message->jobApplicationId,
                $message->jobOfferId,
            ),
        ));
    }
}
