<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

/**
 * @package App\Recruit\Domain\Enum
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
