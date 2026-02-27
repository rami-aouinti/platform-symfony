<?php

declare(strict_types=1);

namespace App\Configuration\Transport\AutoMapper\Configuration;

use App\General\Transport\AutoMapper\RestRequestMapper;

/**
 * RequestMapper.
 *
 * @package App\Configuration\Transport\AutoMapper\Configuration
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'code',
        'keyName',
        'value',
        'status',
    ];
}
