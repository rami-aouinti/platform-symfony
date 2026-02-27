<?php

declare(strict_types=1);

namespace App\General\Transport\AutoMapper;

use App\General\Application\DTO\CrudDtoClassResolver;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;

/**
 * ConventionalRestAutoMapperConfiguration.
 *
 * @package App\General\Transport\AutoMapper
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
abstract class ConventionalRestAutoMapperConfiguration extends RestAutoMapperConfiguration
{
    /**
     * @var class-string
     */
    protected static string $dtoBaseClass;

    /**
     * @return array<int, class-string>
     */
    protected static function requestMapperClasses(): array
    {
        return CrudDtoClassResolver::all(static::$dtoBaseClass);
    }

    public function configure(AutoMapperConfigInterface $config): void
    {
        static::$requestMapperClasses = static::requestMapperClasses();

        parent::configure($config);
    }
}
