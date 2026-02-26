<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Sprint;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Task\Application\Resource\Interfaces\TaskRequestResourceInterface;
use App\Task\Domain\Entity\TaskRequest;
use DateTimeImmutable;
use Throwable;

use function array_filter;
use function array_map;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'startDate',
        'endDate',
        'taskRequests',
    ];

    public function __construct(private readonly TaskRequestResourceInterface $taskRequestResource)
    {
    }

    protected function transformStartDate(?string $startDate): ?DateTimeImmutable
    {
        return $startDate !== null && $startDate !== '' ? new DateTimeImmutable($startDate) : null;
    }

    protected function transformEndDate(?string $endDate): ?DateTimeImmutable
    {
        return $endDate !== null && $endDate !== '' ? new DateTimeImmutable($endDate) : null;
    }

    /**
     * @param array<int, string>|string|null $taskRequests
     *
     * @return array<int, TaskRequest>
     */
    protected function transformTaskRequests(array|string|null $taskRequests): array
    {
        $taskRequestIds = (array) $taskRequests;

        return array_values(array_filter(array_map(function (string $taskRequestId): ?TaskRequest {
            try {
                return $this->taskRequestResource->getReference($taskRequestId);
            } catch (Throwable) {
                return null;
            }
        }, $taskRequestIds)));
    }
}
