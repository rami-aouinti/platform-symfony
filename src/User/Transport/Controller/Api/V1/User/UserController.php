<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\User;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\User\Application\DTO\User\UserCreate;
use App\User\Application\DTO\User\UserPatch;
use App\User\Application\DTO\User\UserUpdate;
use App\User\Application\Resource\UserResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\User
 *
 * @method UserResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/api/v1/admin/users')]
#[Route(
    path: '/v1/admin/users',
)]
/**
 * API controller for UserController endpoints.
 *
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 * @package App\User\Transport\Controller\Api\V1\User
 */

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Admin - User')]
class UserController extends Controller
{
    private const string READ_CACHE_SCOPE = 'user';
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\FindOneAction;
    use Actions\Admin\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;
    use Actions\Admin\SchemaAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => UserCreate::class,
        Controller::METHOD_UPDATE => UserUpdate::class,
        Controller::METHOD_PATCH => UserPatch::class,
    ];


    /**
     * @return array<string, mixed>
     */
    protected function getSchemaFieldConfiguration(): array
    {
        return [
            'displayable' => [
                'firstName',
                'lastName',
                'email',
            ],
            'editable' => [
                'firstName',
                'lastName',
            ],
            'creatable' => [
                'fields' => [
                    'username',
                    'email',
                    'firstName',
                    'lastName',
                    'password',
                ],
                'required' => [
                    'username',
                    'email',
                    'firstName',
                    'lastName',
                    'password',
                ],
            ],
        ];
    }

    public function __construct(
        UserResource $resource,
    ) {
        parent::__construct($resource);
    }

    protected function getReadCacheScope(): ?string
    {
        return self::READ_CACHE_SCOPE;
    }
}
