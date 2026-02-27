<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\ConversationParticipant;

/**
 * @package App\Chat\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface ConversationParticipantRepositoryInterface
{
    /**
     * @return ConversationParticipant[]
     */
    public function findByConversationId(string $conversationId): array;
    /**
     * @return string[]
     */
    public function findConversationIdsByUserId(string $userId): array;
}
