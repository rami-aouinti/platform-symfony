<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Domain\Entity\ChatMessage;
use DateTimeImmutable;

/**
 * @package App\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ChatMessageView
{
    private string $id;
    private string $senderId;
    private string $content;
    private ?DateTimeImmutable $createdAt;

    public function __construct(ChatMessage $message)
    {
        $this->id = $message->getId();
        $this->senderId = $message->getSender()?->getId() ?? '';
        $this->content = $message->getContent();
        $this->createdAt = $message->getCreatedAt();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSenderId(): string
    {
        return $this->senderId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
