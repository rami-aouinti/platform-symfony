<?php

declare(strict_types=1);

namespace App\Chat\Application\DTO\Chat;

use App\Chat\Domain\Entity\Conversation;

class ConversationView
{
    private string $id;
    private string $jobApplicationId;

    /**
     * @var string[]
     */
    private array $participantUserIds;

    /**
     * @var ChatMessageView[]
     */
    private array $messages;

    /**
     * @param ChatMessageView[] $messages
     */
    public function __construct(Conversation $conversation, array $messages)
    {
        $this->id = $conversation->getId();
        $this->jobApplicationId = $conversation->getJobApplication()?->getId() ?? '';
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

    public function getJobApplicationId(): string
    {
        return $this->jobApplicationId;
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
