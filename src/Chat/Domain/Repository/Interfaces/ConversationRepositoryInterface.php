<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\Conversation;

interface ConversationRepositoryInterface
{
    public function findOneByJobApplicationId(string $jobApplicationId): ?Conversation;
}
