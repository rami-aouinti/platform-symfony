<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Domain\Entity\Conversation;
use Symfony\Component\Serializer\Annotation\Groups;

class ConversationView
{
    #[Groups(['default'])]
    private string $id;

    /**
     * @var ChatUserView[]
     */
    #[Groups(['default'])]
    private array $participants;

    /**
     * @var ChatMessageView[]
     */
    #[Groups(['default'])]
    private array $messages;

    /**
     * @param ChatMessageView[] $messages
     */
    public function __construct(Conversation $conversation, array $messages, string $currentUserId)
    {
        $this->id = $conversation->getId();
        $this->participants = $conversation->getParticipants()
            ->map(static fn ($participant): ?ChatUserView => $participant->getUser() !== null ? new ChatUserView($participant->getUser(), $currentUserId) : null)
            ->filter(static fn (?ChatUserView $user): bool => $user instanceof ChatUserView)
            ->toArray();
        $this->messages = $messages;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return ChatUserView[]
     */
    public function getParticipants(): array
    {
        return $this->participants;
    }

    /**
     * @return ChatMessageView[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
