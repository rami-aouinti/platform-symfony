<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\TaskRequest;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Task\Application\DTO\TaskRequest\TaskRequestCreate;
use App\Task\Application\DTO\TaskRequest\TaskRequestPatch;
use App\Task\Application\DTO\TaskRequest\TaskRequestUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Task\Transport\AutoMapper\TaskRequest
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        TaskRequestCreate::class,
        TaskRequestUpdate::class,
        TaskRequestPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
