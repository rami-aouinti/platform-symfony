<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel\Interfaces;

interface PushNotificationInterface
{
    public function send(string $recipient, string $subject, string $content): void;
}
