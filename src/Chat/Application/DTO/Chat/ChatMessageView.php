<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Application\Support\Utf8Sanitizer;
use App\Chat\Domain\Entity\ChatMessage;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

class ChatMessageView
{
    #[Groups(['default'])]
    private string $id;

    #[Groups(['default'])]
    private ?ChatUserView $sender;

    #[Groups(['default'])]
    private bool $isFromCurrentUser;

    #[Groups(['default'])]
    private string $content;

    #[Groups(['default'])]
    private ?DateTimeImmutable $createdAt;

    #[Groups(['default'])]
    private ?DateTimeImmutable $readAt;

    #[Groups(['default'])]
    private bool $isRead;

    /**
     * @var array<int, array<string, mixed>>
     */
    #[Groups(['default'])]
    private array $attachments;

    /**
     * @var ChatReactionView[]
     */
    #[Groups(['default'])]
    private array $reactions;

    public function __construct(ChatMessage $message, string $currentUserId)
    {
        $this->id = $message->getId();
        $sender = $message->getSender();
        $this->sender = $sender !== null ? new ChatUserView($sender, $currentUserId) : null;
        $this->isFromCurrentUser = $sender !== null && $sender->getId() === $currentUserId;
        $this->content = Utf8Sanitizer::sanitizeString($message->getContent());
        $this->createdAt = $message->getCreatedAt();
        $this->readAt = $message->getReadAt();
        $this->isRead = $this->isFromCurrentUser || $this->readAt !== null;
        $this->attachments = Utf8Sanitizer::sanitizeArray($message->getAttachments());
        $this->reactions = $message->getReactions()->map(
            static fn ($reaction): ChatReactionView => new ChatReactionView($reaction, $currentUserId),
        )->toArray();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSender(): ?ChatUserView
    {
        return $this->sender;
    }

    public function isFromCurrentUser(): bool
    {
        return $this->isFromCurrentUser;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function isRead(): bool
    {
        return $this->isRead;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    /**
     * @return ChatReactionView[]
     */
    public function getReactions(): array
    {
        return $this->reactions;
    }
}
