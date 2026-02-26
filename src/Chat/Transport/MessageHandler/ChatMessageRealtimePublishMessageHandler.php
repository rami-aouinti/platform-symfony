<?php

declare(strict_types=1);

namespace App\Chat\Transport\MessageHandler;

use App\Chat\Application\Service\Realtime\Interfaces\ChatRealtimePublisherInterface;
use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Message\ChatMessageRealtimePublishMessage;
use App\Chat\Domain\Repository\Interfaces\ChatMessageRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ChatMessageRealtimePublishMessageHandler
{
    public function __construct(
        private ChatMessageRepositoryInterface $chatMessageRepository,
        private ChatRealtimePublisherInterface $chatRealtimePublisher,
    ) {
    }

    public function __invoke(ChatMessageRealtimePublishMessage $message): void
    {
        $chatMessage = $this->chatMessageRepository->find($message->messageId);

        if (!$chatMessage instanceof ChatMessage) {
            return;
        }

        $this->chatRealtimePublisher->publish($chatMessage);
    }
}
