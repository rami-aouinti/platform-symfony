<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Sprint;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Task\Application\DTO\Sprint\SprintCreate;
use App\Task\Application\DTO\Sprint\SprintPatch;
use App\Task\Application\DTO\Sprint\SprintUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Task\Transport\AutoMapper\Sprint
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        SprintCreate::class,
        SprintUpdate::class,
        SprintPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
