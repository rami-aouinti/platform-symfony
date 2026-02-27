<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;

final class FilterTasksForCurrentUser
{
    public function __construct(
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
    }

    public function execute(array &$criteria): void
    {
        $currentUser = $this->currentTaskUserProvider->getCurrentUser();

        if ($this->taskAccessService->isAdminLike($currentUser)) {
            return;
        }

        $criteria['owner'] = $currentUser;
    }
}
