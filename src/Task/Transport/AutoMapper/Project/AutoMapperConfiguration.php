<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Project;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Task\Application\DTO\Project\ProjectCreate;
use App\Task\Application\DTO\Project\ProjectPatch;
use App\Task\Application\DTO\Project\ProjectUpdate;

/**
 * @package App\Task\Transport\AutoMapper\Project
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    /**
     * Classes to use specified request mapper.
     *
     * @var array<int, class-string>
     */
    protected static array $requestMapperClasses = [
        ProjectCreate::class,
        ProjectUpdate::class,
        ProjectPatch::class,
    ];

    public function __construct(
        RequestMapper $requestMapper,
    ) {
        parent::__construct($requestMapper);
    }
}
