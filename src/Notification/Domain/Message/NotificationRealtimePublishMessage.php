<?php

declare(strict_types=1);

namespace App\Notification\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

readonly class NotificationRealtimePublishMessage implements MessageHighInterface
{
    public function __construct(
        public string $userId,
        public string $title,
        public string $message,
    ) {
    }
}

