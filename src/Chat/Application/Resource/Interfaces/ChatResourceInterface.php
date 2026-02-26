<?php

declare(strict_types=1);

namespace App\Chat\Application\Resource\Interfaces;

use App\Chat\Application\DTO\Chat\ConversationCreate;
use App\Chat\Application\DTO\Chat\ConversationView;

interface ChatResourceInterface
{
    public function createConversation(ConversationCreate $dto): ConversationView;

    public function getConversation(string $conversationId): ConversationView;

    public function sendMessage(string $conversationId, string $content): ConversationView;
}
