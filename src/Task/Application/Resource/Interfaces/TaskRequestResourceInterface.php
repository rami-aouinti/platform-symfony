<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskStatus;

interface TaskRequestResourceInterface extends RestResourceInterface
{
    public function changeRequestedStatus(string $id, TaskStatus $requestedStatus): TaskRequest;

    /**
     * @return array<int|string, mixed>
     */
    public function listBySprintGroupedByTask(string $sprintId, ?string $userId = null): array;

    public function assignRequester(string $id, string $requesterId): TaskRequest;

    public function assignReviewer(string $id, string $reviewerId): TaskRequest;

    public function assignSprint(string $id, ?string $sprintId): TaskRequest;
}
