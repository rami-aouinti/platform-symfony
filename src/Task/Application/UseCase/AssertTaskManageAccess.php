<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\Task;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AssertTaskManageAccess
{
    public function __construct(
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
    }

    public function execute(Task $task): void
    {
        $currentUser = $this->currentTaskUserProvider->getCurrentUser();

        if (!$this->taskAccessService->canManageTask($currentUser, $task)) {
            throw new AccessDeniedHttpException('Only task owner can manage this task.');
        }
    }
}
