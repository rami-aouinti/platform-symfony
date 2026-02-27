<?php

declare(strict_types=1);

namespace App\Chat\Application\Service\Realtime;

use App\Chat\Application\Service\Realtime\Interfaces\ChatRealtimePublisherInterface;
use App\Chat\Domain\Entity\ChatMessage;

use function class_exists;
use function is_object;
use function json_encode;
use function method_exists;
use function sprintf;

/**
 * @package App\Chat\Application\Service\Realtime
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

readonly class ChatRealtimePublisher implements ChatRealtimePublisherInterface
{
    public function __construct(
        private ?object $hub = null,
    ) {
    }

    public function publish(ChatMessage $message): void
    {
        $conversationId = $message->getConversation()?->getId();
        $senderId = $message->getSender()?->getId();
        $createdAt = $message->getCreatedAt();

        if ($conversationId === null || $senderId === null || $createdAt === null) {
            return;
        }

        $payload = json_encode([
            'messageId' => $message->getId(),
            'conversationId' => $conversationId,
            'senderId' => $senderId,
            'body' => $message->getContent(),
            'createdAt' => $createdAt->format(DATE_ATOM),
        ]);

        if ($payload === false || !$this->canPublish()) {
            return;
        }

        $updateClass = 'Symfony\\Component\\Mercure\\Update';

        $this->hub->publish(new $updateClass(
            sprintf('/conversations/%s', $conversationId),
            $payload,
        ));
    }

    private function canPublish(): bool
    {
        return is_object($this->hub)
            && class_exists('Symfony\\Component\\Mercure\\Update')
            && method_exists($this->hub, 'publish');
    }
}
