<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogComment;

use App\Blog\Application\DTO\BlogComment\BlogCommentCreate;
use App\Blog\Application\DTO\BlogComment\BlogCommentPatch;
use App\Blog\Application\DTO\BlogComment\BlogCommentUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        BlogCommentCreate::class,
        BlogCommentUpdate::class,
        BlogCommentPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
