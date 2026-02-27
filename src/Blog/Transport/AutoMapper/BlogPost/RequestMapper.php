<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogPost;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\General\Transport\AutoMapper\PropertiesConventionTrait;

/**
 * RequestMapper.
 *
 * @package App\Blog\Transport\AutoMapper\BlogPost
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
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
