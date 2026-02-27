<?php

declare(strict_types=1);

namespace App\Quiz\Transport\AutoMapper\Quiz;

use App\General\Transport\AutoMapper\RestRequestMapper;

/**
 * RequestMapper.
 *
 * @package App\Quiz\Transport\AutoMapper\Quiz
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title',
        'description',
        'category',
        'difficulty',
        'timeLimit',
        'isPublished',
        'startsAt',
        'endsAt',
        'owner',
    ];
}
