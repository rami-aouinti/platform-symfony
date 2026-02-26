<?php

declare(strict_types=1);

namespace App\Company\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum CompanyMembershipStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case INVITED = 'invited';
    case ACTIVE = 'active';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::INVITED => $target === self::ACTIVE,
            self::ACTIVE => false,
        };
    }
}
