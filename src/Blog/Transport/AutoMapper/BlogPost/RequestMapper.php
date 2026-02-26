<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPost;

use App\General\Transport\AutoMapper\RestRequestMapper;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
    ];
}
