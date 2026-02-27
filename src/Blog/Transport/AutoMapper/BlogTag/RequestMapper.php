<?php

declare(strict_types=1);

namespace App\Blog\Transport\AutoMapper\BlogTag;

use App\General\Transport\AutoMapper\RestRequestMapper;

/**
 * RequestMapper.
 *
 * @package App\Blog\Transport\AutoMapper\BlogTag
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'name',
        'slug',
    ];
}
