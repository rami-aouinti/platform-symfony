<?php

declare(strict_types=1);

namespace App\Chat\Application\Service\Realtime\Interfaces;

use App\Chat\Domain\Entity\ChatMessage;

interface ChatRealtimePublisherInterface
{
    public function publish(ChatMessage $message): void;
}
