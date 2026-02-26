<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\TaskRequest;

interface TaskRequestResourceInterface extends RestResourceInterface
{
    public function approve(string $id): TaskRequest;

    public function reject(string $id): TaskRequest;

    public function cancel(string $id): TaskRequest;
}
