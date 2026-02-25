<?php

declare(strict_types=1);

namespace App\Company\Transport\MessageHandler;

use App\Company\Domain\Message\CompanyCreatedMessage;
use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface;
use App\User\Infrastructure\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

use function sprintf;

#[AsMessageHandler]
readonly class CompanyCreatedMessageHandler
{
    public function __construct(
        private NotificationChannelServiceInterface $notificationChannelService,
        private NotificationRepositoryInterface $notificationRepository,
        private UserRepository $userRepository,
        private HubInterface $hub,
    ) {
    }

    public function __invoke(CompanyCreatedMessage $message): void
    {
        $owner = $this->userRepository->find($message->ownerId);

        if ($owner === null) {
            return;
        }

        $subject = sprintf('Société "%s" créée', $message->companyLegalName);
        $content = sprintf('Votre société "%s" a bien été créée.', $message->companyLegalName);

        $this->notificationChannelService->sendEmailNotification($message->ownerEmail, $subject, $content);
        $this->notificationChannelService->sendPushNotification($message->ownerEmail, $subject, $content);

        $notification = (new Notification($owner))
            ->setType('company_created')
            ->setTitle($subject)
            ->setMessage($content);

        $this->notificationRepository->save($notification);

        $this->hub->publish(new Update(
            sprintf('/users/%s/notifications', $message->ownerId),
            sprintf('{"type":"company_created","companyId":"%s"}', $message->companyId),
        ));
    }
}
