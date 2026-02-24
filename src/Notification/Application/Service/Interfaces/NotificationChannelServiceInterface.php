<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Interfaces;

interface NotificationChannelServiceInterface
{
    public function sendEmailNotification(string $to, string $subject, string $content): void;

    public function sendPushNotification(string $recipient, string $subject, string $content): void;

    public function sendSmsNotification(string $phoneNumber, string $content): void;
}
