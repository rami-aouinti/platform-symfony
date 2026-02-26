<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Sprint;

use App\General\Transport\AutoMapper\RestRequestMapper;
use DateTimeImmutable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'startDate',
        'endDate',
    ];

    protected function transformStartDate(?string $startDate): ?DateTimeImmutable
    {
        return $startDate !== null && $startDate !== '' ? new DateTimeImmutable($startDate) : null;
    }

    protected function transformEndDate(?string $endDate): ?DateTimeImmutable
    {
        return $endDate !== null && $endDate !== '' ? new DateTimeImmutable($endDate) : null;
    }
}
