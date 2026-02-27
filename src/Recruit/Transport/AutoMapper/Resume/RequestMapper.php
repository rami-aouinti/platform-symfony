<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\Resume;

use App\General\Transport\AutoMapper\RestRequestMapper;

/**
 * RequestMapper.
 *
 * @package App\Recruit\Transport\AutoMapper\Resume
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title',
        'summary',
        'isPublic',
    ];
}
