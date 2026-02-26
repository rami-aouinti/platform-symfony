<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum OfferStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::DRAFT => $target !== self::DRAFT,
            self::PUBLISHED => $target === self::ARCHIVED,
            self::ARCHIVED => false,
        };
    }
}
