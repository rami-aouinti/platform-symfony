<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Enum;

enum ApplicationStatus: string
{
    case PENDING = 'PENDING';
    case ACCEPTED = 'ACCEPTED';
    case REJECTED = 'REJECTED';
    case WITHDRAWN = 'WITHDRAWN';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::PENDING => $target !== self::PENDING,
            self::ACCEPTED, self::REJECTED, self::WITHDRAWN => false,
        };
    }
}
