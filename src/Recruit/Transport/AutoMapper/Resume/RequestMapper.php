<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\Resume;

use App\General\Transport\AutoMapper\RestRequestMapper;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title',
        'summary',
        'experiences',
        'education',
        'skills',
        'links',
        'isPublic',
    ];
}
