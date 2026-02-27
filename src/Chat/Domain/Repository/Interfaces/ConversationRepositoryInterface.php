<?php

declare(strict_types=1);

namespace App\Chat\Domain\Repository\Interfaces;

use App\Chat\Domain\Entity\Conversation;

/**
 * @package App\Chat\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface ConversationRepositoryInterface
{
    public function findOneByJobApplicationId(string $jobApplicationId): ?Conversation;
}
