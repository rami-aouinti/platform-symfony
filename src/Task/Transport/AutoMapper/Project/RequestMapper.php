<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\Project;

use App\General\Transport\AutoMapper\RestRequestMapper;

/**
 * @package App\Task\Transport\AutoMapper\Project
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class RequestMapper extends RestRequestMapper
{
    /**
     * Properties to map to destination object.
     *
     * @var array<int, non-empty-string>
     */
    protected static array $properties = [
        'name',
        'description',
        'status',
    ];
}
