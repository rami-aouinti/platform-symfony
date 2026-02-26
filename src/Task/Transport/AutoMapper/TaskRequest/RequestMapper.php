<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\TaskRequest;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Task\Application\Resource\TaskResource;
use App\Task\Domain\Entity\Task;
use DateTimeImmutable;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'task',
        'type',
        'requestedStatus',
        'note',
        'status',
        'resolvedAt',
    ];

    public function __construct(private readonly TaskResource $taskResource)
    {
    }

    protected function transformTask(?string $task): ?Task
    {
        if ($task === null || $task === '') {
            return null;
        }

        try {
            return $this->taskResource->getReference($task);
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformResolvedAt(?string $resolvedAt): ?DateTimeImmutable
    {
        return $resolvedAt !== null && $resolvedAt !== '' ? new DateTimeImmutable($resolvedAt) : null;
    }
}
