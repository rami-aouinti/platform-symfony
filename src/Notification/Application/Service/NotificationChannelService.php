<?php

declare(strict_types=1);

namespace App\Notification\Application\Service;

use App\Notification\Application\Service\Channel\Interfaces\EmailNotificationInterface;
use App\Notification\Application\Service\Channel\Interfaces\PushNotificationInterface;
use App\Notification\Application\Service\Channel\Interfaces\SmsNotificationInterface;
use App\Notification\Application\Service\Interfaces\NotificationChannelServiceInterface;

readonly class NotificationChannelService implements NotificationChannelServiceInterface
{
    public function __construct(
        private EmailNotificationInterface $emailNotification,
        private PushNotificationInterface $pushNotification,
        private SmsNotificationInterface $smsNotification,
    ) {
    }

    public function sendEmailNotification(string $to, string $subject, string $content): void
    {
        $this->emailNotification->send($to, $subject, $content);
    }

    public function sendPushNotification(string $recipient, string $subject, string $content): void
    {
        $this->pushNotification->send($recipient, $subject, $content);
    }

    public function sendSmsNotification(string $phoneNumber, string $content): void
    {
        $this->smsNotification->send($phoneNumber, $content);
    }
}
