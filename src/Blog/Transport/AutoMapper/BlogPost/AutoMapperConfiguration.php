<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPost;

use App\Blog\Application\DTO\BlogPost\BlogPost;
use App\General\Transport\AutoMapper\ConventionalRestAutoMapperConfiguration;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Blog\Transport\AutoMapper\BlogPost
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends ConventionalRestAutoMapperConfiguration
{
    protected static string $dtoBaseClass = BlogPost::class;

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
