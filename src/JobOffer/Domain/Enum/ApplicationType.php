<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Enum;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum ApplicationType: string
{
    case INTERNAL = 'internal';
    case EMAIL = 'email';
    case EXTERNAL_LINK = 'external-link';

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
