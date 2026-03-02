<?php

declare(strict_types=1);

namespace App\Chat\Application\Support;

use function iconv;
use function is_array;
use function is_string;
use function mb_check_encoding;

final class Utf8Sanitizer
{
    public static function sanitizeString(string $value): string
    {
        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $converted = iconv('UTF-8', 'UTF-8//IGNORE', $value);

        return is_string($converted) ? $converted : '';
    }

    /**
     * @param array<mixed> $value
     *
     * @return array<mixed>
     */
    public static function sanitizeArray(array $value): array
    {
        foreach ($value as $key => $item) {
            if (is_string($item)) {
                $value[$key] = self::sanitizeString($item);
                continue;
            }

            if (is_array($item)) {
                $value[$key] = self::sanitizeArray($item);
            }
        }

        return $value;
    }
}

