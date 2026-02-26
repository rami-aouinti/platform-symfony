<?php

declare(strict_types=1);

namespace App\Notification\Transport\MessageHandler;

use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;
use App\Notification\Domain\Message\NotificationRealtimePublishMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @package App\Notification
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[AsMessageHandler]
readonly class NotificationRealtimePublishMessageHandler
{
    public function __construct(
        private NotificationChannelServiceInterface $notificationChannelService,
    ) {
    }

    public function __invoke(NotificationRealtimePublishMessage $message): void
    {
        $this->notificationChannelService->sendPushNotification($message->userId, $message->title, $message->message);
    }
}
