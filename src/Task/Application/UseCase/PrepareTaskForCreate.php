<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\Task;

final class PrepareTaskForCreate
{
    public function __construct(
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
    ) {
    }

    public function execute(Task $task): void
    {
        $task->setOwner($this->currentTaskUserProvider->getCurrentUser());
    }
}
