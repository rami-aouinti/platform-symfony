<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskStatus;

interface TaskRequestResourceInterface extends RestResourceInterface
{
    public function changeRequestedStatus(string $id, TaskStatus $requestedStatus): TaskRequest;

    public function approve(string $id): TaskRequest;

    public function reject(string $id): TaskRequest;

    public function cancel(string $id): TaskRequest;
}
