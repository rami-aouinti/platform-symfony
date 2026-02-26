<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum RemoteMode: string
{
    case ON_SITE = 'on-site';
    case HYBRID = 'hybrid';
    case REMOTE = 'remote';

    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
