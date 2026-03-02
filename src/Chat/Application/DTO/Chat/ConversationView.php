<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Domain\Entity\Conversation;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @package App\Chat\Application\DTO\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ConversationView
{
    #[Groups(['default'])]
    private string $id;

    /**
     * @var string[]
     */
    #[Groups(['default'])]
    private array $participantUserIds;

    /**
     * @var ChatMessageView[]
     */
    #[Groups(['default'])]
    private array $messages;

    /**
     * @param ChatMessageView[] $messages
     */
    public function __construct(Conversation $conversation, array $messages)
    {
        $this->id = $conversation->getId();
        $this->participantUserIds = $conversation->getParticipants()
            ->map(static fn ($participant): string => $participant->getUser()?->getId() ?? '')
            ->filter(static fn (string $userId): bool => $userId !== '')
            ->toArray();
        $this->messages = $messages;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getParticipantUserIds(): array
    {
        return $this->participantUserIds;
    }

    /**
     * @return ChatMessageView[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
