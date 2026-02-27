<?php

declare(strict_types=1);

namespace App\General\Application\DTO;

use App\General\Transport\Rest\Controller;
use InvalidArgumentException;

use function array_key_exists;
use function sprintf;

/**
 * CrudDtoClassResolver.
 *
 * @package App\General\Application\DTO
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class CrudDtoClassResolver
{
    /**
     * @var array<string, non-empty-string>
     */
    private const METHOD_SUFFIX_MAP = [
        Controller::METHOD_CREATE => 'Create',
        Controller::METHOD_UPDATE => 'Update',
        Controller::METHOD_PATCH => 'Patch',
    ];

    /**
     * @param class-string $dtoBaseClass
     */
    public static function forMethod(string $dtoBaseClass, string $method): string
    {
        if (!array_key_exists($method, self::METHOD_SUFFIX_MAP)) {
            throw new InvalidArgumentException(sprintf('Method "%s" is not mapped to a DTO suffix.', $method));
        }

        return $dtoBaseClass . self::METHOD_SUFFIX_MAP[$method];
    }

    public static function supportsMethod(string $method): bool
    {
        return array_key_exists($method, self::METHOD_SUFFIX_MAP);
    }

    /**
     * @param class-string $dtoBaseClass
     *
     * @return array<int, class-string>
     */
    public static function all(string $dtoBaseClass): array
    {
        return [
            self::forMethod($dtoBaseClass, Controller::METHOD_CREATE),
            self::forMethod($dtoBaseClass, Controller::METHOD_UPDATE),
            self::forMethod($dtoBaseClass, Controller::METHOD_PATCH),
        ];
    }
}
