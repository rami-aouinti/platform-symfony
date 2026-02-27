<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ChangeTaskStatus
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly TaskAccessServiceInterface $taskAccessService,
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
    ) {
    }

    public function execute(string $id, TaskStatus $status): Task
    {
        $task = $this->taskRepository->find($id);

        if (!$task instanceof Task) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Task not found.');
        }

        $user = $this->currentTaskUserProvider->getCurrentUser();

        if (!$this->taskAccessService->canManageTask($user, $task)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Only task owner can manage this task.');
        }

        $task->setStatus($status);
        $this->taskRepository->save($task);

        return $task;
    }
}
