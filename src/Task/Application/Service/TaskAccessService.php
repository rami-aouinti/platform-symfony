<?php

declare(strict_types=1);

namespace App\Task\Application\Service;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
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
        return $this->isAdminLike($user) || $task->getOwner()?->getId() === $user->getId();
    }

    public function canManageProject(User $user, Project $project): bool
    {
        return $this->isAdminLike($user) || $project->getOwner()?->getId() === $user->getId();
    }
}
