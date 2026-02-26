<?php

declare(strict_types=1);

namespace App\General\Domain\Enum\Traits;

use function array_column;

/**
 * @package App\General
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait GetValues
{
    /**
     * @return array<int, string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
