<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum JobApplicationStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PENDING => $target !== self::PENDING,
            self::ACCEPTED, self::REJECTED, self::WITHDRAWN => false,
        };
    }
}
