<?php

declare(strict_types=1);

namespace App\Chat\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

/**
 * @package App\Chat\Domain\Message
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

readonly class ChatMessageRealtimePublishMessage implements MessageHighInterface
{
    public function __construct(
        public string $messageId,
    ) {
    }
}
