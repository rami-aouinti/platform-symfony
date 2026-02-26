<?php

declare(strict_types=1);

namespace App\Media\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum MediaStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case ACTIVE = 'active';
    case ARCHIVED = 'archived';

    public function canTransitionTo(self $target): bool
    {
        return $this !== $target;
    }
}
