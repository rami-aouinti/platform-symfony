<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\ChatMessageReaction;
use App\User\Domain\Entity\User;

interface ChatMessageReactionRepositoryInterface
{
    public function findOneByMessageUserReaction(ChatMessage $message, User $user, string $reaction): ?ChatMessageReaction;

    public function save(ChatMessageReaction $reaction, ?bool $flush = null, ?string $entityManagerName = null): self;

    public function remove(ChatMessageReaction $reaction, ?bool $flush = null, ?string $entityManagerName = null): self;
}
