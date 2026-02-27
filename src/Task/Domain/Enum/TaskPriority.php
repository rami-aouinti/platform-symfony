<?php

declare(strict_types=1);

namespace App\Task\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * TaskPriority.
 *
 * @package App\Task\Domain\Enum
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
enum TaskPriority: string implements DatabaseEnumInterface
{
    use GetValues;

    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
}
