<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Enum;

enum ExperienceLevel: string
{
    case INTERN = 'intern';
    case JUNIOR = 'junior';
    case MID = 'mid';
    case SENIOR = 'senior';
    case LEAD = 'lead';
    case PRINCIPAL = 'principal';

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
