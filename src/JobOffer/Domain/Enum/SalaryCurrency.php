<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Enum;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum SalaryCurrency: string
{
    case EUR = 'EUR';
    case USD = 'USD';
    case GBP = 'GBP';
    case CHF = 'CHF';
    case CAD = 'CAD';
    case BRL = 'BRL';

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
