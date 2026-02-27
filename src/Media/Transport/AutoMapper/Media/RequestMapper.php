<?php

declare(strict_types=1);

namespace App\Media\Transport\AutoMapper\Media;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\General\Transport\AutoMapper\PropertiesConventionTrait;

class RequestMapper extends RestRequestMapper
{
    use PropertiesConventionTrait;

    private const PROPERTIES = [
        'name',
        'path',
        'mimeType',
        'size',
        'status',
    ];
}
