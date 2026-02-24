<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel\Interfaces;

interface SmsNotificationInterface
{
    public function send(string $phoneNumber, string $content): void;
}
