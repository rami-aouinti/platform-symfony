<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\ChatMessage;

/**
 * @package App\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface ChatMessageRepositoryInterface
{
    public function findById(string $id): ?ChatMessage;

    /**
     * @return ChatMessage[]
     */
    public function findByConversationId(string $conversationId): array;
}
