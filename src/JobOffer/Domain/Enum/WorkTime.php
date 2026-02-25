<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Enum;

enum WorkTime: string
{
    case FULL_TIME = 'full-time';
    case PART_TIME = 'part-time';

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
