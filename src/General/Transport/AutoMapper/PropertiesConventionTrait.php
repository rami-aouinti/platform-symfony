<?php

declare(strict_types=1);

namespace App\General\Transport\AutoMapper;

/**
 * PropertiesConventionTrait.
 *
 * @package App\General\Transport\AutoMapper
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
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
