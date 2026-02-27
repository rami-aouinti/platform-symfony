<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel\Interfaces;

/**
 * @package App\Notification\Application\Service\Channel\Interfaces* @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface SmsNotificationInterface
{
    public function send(string $phoneNumber, string $content): void;
}
