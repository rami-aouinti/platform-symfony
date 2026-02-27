<?php

declare(strict_types=1);

namespace App\Task\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * TaskRequestType.
 *
 * @package App\Task\Domain\Enum
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
enum TaskRequestType: string implements DatabaseEnumInterface
{
    use GetValues;

    case STATUS_CHANGE = 'status_change';
}
