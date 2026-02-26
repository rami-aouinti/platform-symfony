<?php

declare(strict_types=1);

namespace App\Configuration\Transport\AutoMapper\Configuration;

use App\Configuration\Application\DTO\Configuration\ConfigurationCreate;
use App\Configuration\Application\DTO\Configuration\ConfigurationPatch;
use App\Configuration\Application\DTO\Configuration\ConfigurationUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ConfigurationCreate::class,
        ConfigurationUpdate::class,
        ConfigurationPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
