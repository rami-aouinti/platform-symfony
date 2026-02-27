<?php

declare(strict_types=1);

namespace App\Task\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * ProjectStatus.
 *
 * @package App\Task\Domain\Enum
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
enum ProjectStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case ACTIVE = 'active';
    case ARCHIVED = 'archived';

    public function canTransitionTo(self $target): bool
    {
        return $this !== $target;
    }
}
