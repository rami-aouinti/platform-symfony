<?php

declare(strict_types=1);

namespace App\General\Transport\Rest;

use App\General\Application\DTO\CrudDtoClassResolver;
use App\General\Transport\Rest\Traits\Actions;
use LogicException;

use function in_array;
use function sprintf;

/**
 * CrudController.
 *
 * @package App\General\Transport\Rest
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
abstract class CrudController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    /**
     * @var class-string
     */
    protected static string $dtoBaseClass;

    /**
     * @var array<int, string>
     */
    protected static array $enabledMethods = [
        self::METHOD_CREATE,
        self::METHOD_UPDATE,
        self::METHOD_PATCH,
    ];

    public function getDtoClass(?string $method = null): string
    {
        if ($method === null || !CrudDtoClassResolver::supportsMethod($method)) {
            return parent::getDtoClass($method);
        }

        if (!in_array($method, static::$enabledMethods, true)) {
            throw new LogicException(sprintf('Method "%s" is disabled for "%s".', $method, static::class));
        }

        return CrudDtoClassResolver::forMethod(static::$dtoBaseClass, $method);
    }
}
