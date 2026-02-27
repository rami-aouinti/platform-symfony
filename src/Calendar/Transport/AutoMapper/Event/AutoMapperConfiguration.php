<?php

declare(strict_types=1);

namespace App\Calendar\Transport\AutoMapper\Event;

use App\Calendar\Application\DTO\Event\Event;
use App\General\Transport\AutoMapper\ConventionalRestAutoMapperConfiguration;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Calendar\Transport\AutoMapper\Event
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends ConventionalRestAutoMapperConfiguration
{
    protected static string $dtoBaseClass = Event::class;

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
