<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPost;

use App\Blog\Application\DTO\BlogPost\BlogPostCreate;
use App\Blog\Application\DTO\BlogPost\BlogPostPatch;
use App\Blog\Application\DTO\BlogPost\BlogPostUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        BlogPostCreate::class,
        BlogPostUpdate::class,
        BlogPostPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
