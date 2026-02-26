<?php

declare(strict_types=1);

namespace App\Task\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum TaskRequestStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PENDING => $target !== self::PENDING,
            self::APPROVED, self::REJECTED, self::CANCELLED => false,
        };
    }
}
