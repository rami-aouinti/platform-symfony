<?php

declare(strict_types=1);

namespace App\Blog\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * BlogReferenceType.
 *
 * @package App\Blog\Domain\Enum
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
enum BlogReferenceType: string implements DatabaseEnumInterface
{
    use GetValues;

    case TASK = 'task';
    case TASK_REQUEST = 'task_request';
}
