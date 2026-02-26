<?php

declare(strict_types=1);

namespace App\Media\Transport\AutoMapper\Media;

use App\General\Transport\AutoMapper\RestRequestMapper;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'name',
        'path',
        'mimeType',
        'size',
        'status',
    ];
}
