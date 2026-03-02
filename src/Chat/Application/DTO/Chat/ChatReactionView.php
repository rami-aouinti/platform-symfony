<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Application\Support\Utf8Sanitizer;
use App\Chat\Domain\Entity\ChatMessageReaction;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

class ChatReactionView
{
    #[Groups(['default'])]
    private string $reaction;

    #[Groups(['default'])]
    private ChatUserView $user;

    #[Groups(['default'])]
    private bool $isCurrentUser;

    #[Groups(['default'])]
    private ?DateTimeImmutable $createdAt;

    public function __construct(ChatMessageReaction $reaction, string $currentUserId)
    {
        $user = $reaction->getUser();
        if ($user === null) {
            throw new \RuntimeException('Reaction user is required.');
        }

        $this->reaction = Utf8Sanitizer::sanitizeString($reaction->getReaction());
        $this->user = new ChatUserView($user, $currentUserId);
        $this->isCurrentUser = $user->getId() === $currentUserId;
        $this->createdAt = $reaction->getCreatedAt();
    }

    public function getReaction(): string
    {
        return $this->reaction;
    }

    public function getUser(): ChatUserView
    {
        return $this->user;
    }

    public function isCurrentUser(): bool
    {
        return $this->isCurrentUser;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
