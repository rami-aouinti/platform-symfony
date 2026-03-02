<?php

declare(strict_types=1);

namespace App\Chat\Application\Resource\Interfaces;

use App\Chat\Application\DTO\Chat\ChatMessageView;
use App\Chat\Application\DTO\Chat\ConversationCreate;
use App\Chat\Application\DTO\Chat\ConversationView;
use App\User\Domain\Entity\User;

/**
 * @package App\Chat\Application\Resource\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface ChatResourceInterface
{
    public function createConversation(ConversationCreate $dto, User $sender, User $receiver): ConversationView;

    /**
     * @return ConversationView[]
     */
    public function listConversationsForCurrentUser(): array;

    public function getConversation(string $conversationId): ConversationView;

    public function sendMessage(string $conversationId, string $content): ConversationView;

    /**
     * @return ChatMessageView[]
     */
    public function listMessages(string $conversationId): array;

    /**
     * @param array<int, array<string, mixed>> $attachments
     */
    public function createMessage(string $conversationId, string $content, array $attachments = []): ChatMessageView;

    /**
     * @param array<int, array<string, mixed>> $attachments
     */
    public function updateMessage(string $messageId, string $content, array $attachments = []): ChatMessageView;

    public function deleteMessage(string $messageId): void;

    public function addParticipant(string $conversationId, string $userId): ConversationView;

    public function removeParticipant(string $conversationId, string $userId): ConversationView;

    /**
     * @return ChatMessageView[]
     */
    public function listMessagesForModeration(): array;

    public function deleteMessageForModeration(string $messageId): void;

    public function addReaction(string $messageId, string $reaction): ChatMessageView;

    public function removeReaction(string $messageId, string $reaction): ChatMessageView;
}
