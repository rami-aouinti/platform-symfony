<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Task;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Task\Application\DTO\Task\TaskCreate;
use App\Task\Application\DTO\Task\TaskPatch;
use App\Task\Application\DTO\Task\TaskUpdate;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        TaskCreate::class,
        TaskUpdate::class,
        TaskPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
