<?php

declare(strict_types=1);

namespace App\General\Transport\AutoMapper;

trait PropertiesConventionTrait
{
    /**
     * @return array<int, non-empty-string>
     */
    protected static function properties(): array
    {
        return static::PROPERTIES;
    }
}
