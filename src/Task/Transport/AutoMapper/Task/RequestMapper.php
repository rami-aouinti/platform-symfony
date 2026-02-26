<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Task;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Task\Application\Resource\ProjectResource;
use App\Task\Domain\Entity\Project;
use DateTimeImmutable;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title',
        'description',
        'priority',
        'status',
        'project',
        'dueDate',
        'completedAt',
    ];

    public function __construct(private readonly ProjectResource $projectResource)
    {
    }

    protected function transformProject(?string $project): ?Project
    {
        if ($project === null || $project === '') {
            return null;
        }

        try {
            return $this->projectResource->getReference($project);
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformDueDate(?string $dueDate): ?DateTimeImmutable
    {
        return $dueDate !== null && $dueDate !== '' ? new DateTimeImmutable($dueDate) : null;
    }

    protected function transformCompletedAt(?string $completedAt): ?DateTimeImmutable
    {
        return $completedAt !== null && $completedAt !== '' ? new DateTimeImmutable($completedAt) : null;
    }
}
