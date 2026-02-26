<?php

declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\User\Domain\Entity\User;

use function in_array;

class TaskAccessService implements TaskAccessServiceInterface
{
    public function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    public function canManageTask(User $user, Task $task): bool
    {
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

        $task = $taskRequest->getTask();

        return $task instanceof Task && $this->canManageTask($user, $task);
    }

    public function canReviewTaskRequest(User $user, TaskRequest $taskRequest): bool
    {
        $task = $taskRequest->getTask();

        return $task instanceof Task && $this->canManageTask($user, $task);
    }
}
