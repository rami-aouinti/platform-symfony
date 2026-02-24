<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel;

use App\Notification\Application\Service\Channel\Interfaces\PushNotificationInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\NotifierInterface;

readonly class PushNotification implements PushNotificationInterface
{
    public function __construct(
        private NotifierInterface $notifier,
    ) {
    }

    public function send(string $recipient, string $subject, string $content): void
    {
        $this->notifier->send(new ChatMessage(sprintf('[%s] %s (%s)', $subject, $content, $recipient)));
    }
}
