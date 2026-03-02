<?php

declare(strict_types=1);

namespace App\Chat\Application\Support;

use function iconv;
use function is_array;
use function is_int;
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
        $sanitized = [];

        foreach ($value as $key => $item) {
            $sanitizedKey = is_string($key) ? self::sanitizeString($key) : $key;

            if (!is_string($sanitizedKey) && !is_int($sanitizedKey)) {
                continue;
            }

            if (is_string($item)) {
                $sanitized[$sanitizedKey] = self::sanitizeString($item);
                continue;
            }

            if (is_array($item)) {
                $sanitized[$sanitizedKey] = self::sanitizeArray($item);
                continue;
            }

            $sanitized[$sanitizedKey] = $item;
        }

        return $sanitized;
    }
}

