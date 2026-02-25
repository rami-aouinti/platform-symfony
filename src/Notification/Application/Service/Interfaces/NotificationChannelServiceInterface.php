<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Interfaces;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface NotificationChannelServiceInterface
{
    public function sendEmailNotification(string $to, string $subject, string $content): void;

    public function sendPushNotification(string $userId, string $subject, string $content): void;

    public function sendSmsNotification(string $phoneNumber, string $content): void;
}
