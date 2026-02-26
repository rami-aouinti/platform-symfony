<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogTag;

use App\Blog\Application\DTO\BlogTag\BlogTagCreate;
use App\Blog\Application\DTO\BlogTag\BlogTagPatch;
use App\Blog\Application\DTO\BlogTag\BlogTagUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        BlogTagCreate::class,
        BlogTagUpdate::class,
        BlogTagPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
