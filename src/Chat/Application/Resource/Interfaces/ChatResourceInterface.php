<?php

declare(strict_types=1);

namespace App\Chat\Application\Resource\Interfaces;

use App\Chat\Application\DTO\Chat\ConversationCreate;
use App\Chat\Application\DTO\Chat\ConversationView;

/**
 * @package App\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface ChatResourceInterface
{
    public function createConversation(ConversationCreate $dto): ConversationView;

    /**
     * @return ConversationView[]
     */
    public function listConversationsForCurrentUser(): array;

    public function getConversation(string $conversationId): ConversationView;

    public function sendMessage(string $conversationId, string $content): ConversationView;
}
