<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum LanguageLevel: string
{
    case NONE = 'none';
    case BASIC = 'basic';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case FLUENT = 'fluent';
    case NATIVE = 'native';

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
