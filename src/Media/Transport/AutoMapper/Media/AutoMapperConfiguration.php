<?php

declare(strict_types=1);

namespace App\Media\Transport\AutoMapper\Media;

use App\General\Transport\AutoMapper\ConventionalRestAutoMapperConfiguration;
use App\Media\Application\DTO\Media\Media;

class AutoMapperConfiguration extends ConventionalRestAutoMapperConfiguration
{
    protected static string $dtoBaseClass = Media::class;

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
