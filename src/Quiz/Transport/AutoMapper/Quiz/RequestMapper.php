<?php

declare(strict_types=1);

namespace App\Quiz\Transport\AutoMapper\Quiz;

use App\General\Transport\AutoMapper\RestRequestMapper;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title',
        'description',
    ];
}
