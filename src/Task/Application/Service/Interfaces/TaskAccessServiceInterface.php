<?php

declare(strict_types=1);

namespace App\Task\Application\Service\Interfaces;

use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
use App\User\Domain\Entity\User;

interface TaskAccessServiceInterface
{
    public function isAdminLike(User $user): bool;

    public function canManageTask(User $user, Task $task): bool;

    public function canManageProject(User $user, Project $project): bool;
}
