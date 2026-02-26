<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\ChatMessage;

interface ChatMessageRepositoryInterface
{
    /**
     * @return ChatMessage[]
     */
    public function findByConversationId(string $conversationId): array;
}
