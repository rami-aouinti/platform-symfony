<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPost;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\General\Transport\AutoMapper\PropertiesConventionTrait;

class RequestMapper extends RestRequestMapper
{
    use PropertiesConventionTrait;

    private const PROPERTIES = [
        'title',
        'slug',
        'excerpt',
        'content',
        'status',
    ];
}
