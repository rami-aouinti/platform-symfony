<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel\Interfaces;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface EmailNotificationInterface
{
    public function send(string $to, string $subject, string $content): void;
}
