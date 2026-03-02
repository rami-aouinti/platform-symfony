<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Domain\Entity\ChatMessage;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @package App\Chat\Application\DTO\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ChatMessageView
{
    #[Groups(['default'])]
    private string $id;

    #[Groups(['default'])]
    private string $senderId;

    #[Groups(['default'])]
    private string $content;

    #[Groups(['default'])]
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
