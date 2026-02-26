<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\ConversationParticipant;

interface ConversationParticipantRepositoryInterface
{
    /**
     * @return ConversationParticipant[]
     */
    public function findByConversationId(string $conversationId): array;
}
