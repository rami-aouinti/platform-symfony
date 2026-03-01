<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\User\Domain\Entity\User;

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

    public function findOneByConversationAndUser(Conversation $conversation, User $user): ?ConversationParticipant;

    public function save(ConversationParticipant $participant, ?bool $flush = null): self;

    public function remove(ConversationParticipant $participant, ?bool $flush = null): self;
}
