<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPostLink;

use App\Blog\Application\DTO\BlogPostLink\BlogPostLinkCreate;
use App\Blog\Application\DTO\BlogPostLink\BlogPostLinkPatch;
use App\Blog\Application\DTO\BlogPostLink\BlogPostLinkUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Blog\Transport\AutoMapper\BlogPostLink
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        BlogPostLinkCreate::class,
        BlogPostLinkUpdate::class,
        BlogPostLinkPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
