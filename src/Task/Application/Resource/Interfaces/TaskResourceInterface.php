<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enum\TaskStatus;

interface TaskResourceInterface extends RestResourceInterface
{
    public function changeStatus(string $id, TaskStatus $status): Task;
}
