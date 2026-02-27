<?php

declare(strict_types=1);

namespace App\Calendar\Transport\AutoMapper\Event;

use App\Calendar\Application\DTO\Event\EventCreate;
use App\Calendar\Application\DTO\Event\EventPatch;
use App\Calendar\Application\DTO\Event\EventUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        EventCreate::class,
        EventUpdate::class,
        EventPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
