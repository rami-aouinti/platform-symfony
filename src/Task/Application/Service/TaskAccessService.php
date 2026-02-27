<?php

declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\User\Domain\Entity\User;

use function in_array;

/**
 * TaskAccessService.
 *
 * @package App\Task\Application\Service
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class TaskAccessService implements TaskAccessServiceInterface
{
    public function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    public function scopeTasksQuery(User $user, array &$criteria): void
    {
        if ($this->isAdminLike($user)) {
            return;
        }

        $criteria['owner'] = $user;
    }

    public function scopeTaskRequestsQuery(User $user, array &$criteria): void
    {
        if ($this->isAdminLike($user)) {
            return;
        }

        $criteria['requester'] = $user;
    }

    public function canManageTask(User $user, Task $task): bool
    {
        // Manage access rules:
        // 1) Admin-like users can manage any task.
        // 2) The task owner can manage their task.
        // 3) The project owner can manage tasks of their project.
        if ($this->isAdminLike($user)) {
            return true;
        }

        if ($task->getOwner()?->getId() === $user->getId()) {
            return true;
        }

        return $task->getProject()?->getOwner()?->getId() === $user->getId();
    }

    public function canManageProject(User $user, Project $project): bool
    {
        return $this->isAdminLike($user) || $project->getOwner()?->getId() === $user->getId();
    }

    public function canViewTask(User $user, Task $task): bool
    {
        return $this->canManageTask($user, $task);
    }

    public function canViewTaskRequest(User $user, TaskRequest $taskRequest): bool
    {
        if ($this->isAdminLike($user)) {
            return true;
        }

        if ($taskRequest->getRequester()?->getId() === $user->getId()) {
            return true;
        }

        if ($taskRequest->getReviewer()?->getId() === $user->getId()) {
            return true;
        }

        $task = $taskRequest->getTask();

        return $task instanceof Task && $this->canViewTask($user, $task);
    }

    public function canReviewTaskRequest(User $user, TaskRequest $taskRequest): bool
    {
        if ($this->isAdminLike($user)) {
            return true;
        }

        if ($taskRequest->getReviewer()?->getId() === $user->getId()) {
            return true;
        }

        $task = $taskRequest->getTask();

        return $task instanceof Task && $this->canManageTask($user, $task);
    }
}
