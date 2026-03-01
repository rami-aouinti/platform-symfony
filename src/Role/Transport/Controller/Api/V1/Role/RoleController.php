<?php

declare(strict_types=1);

namespace App\Role\Transport\Controller\Api\V1\Role;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Role\Application\Resource\RoleResource;
use App\Role\Domain\Enum\Role as RoleEnum;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Role
 *
 * @method RoleResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/api/v1/admin/role')]
#[Route(
    path: '/v1/role',
)]
/**
 * API controller for RoleController endpoints.
 *
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 * @package App\Role\Transport\Controller\Api\V1\Role
 */

#[IsGranted(RoleEnum::ADMIN->value)]
#[OA\Tag(name: 'Role Management')]
class RoleController extends Controller
{
    use Actions\Admin\CountAction;
    use Actions\Admin\FindAction;
    use Actions\Admin\IdsAction;
    use Actions\Admin\SchemaAction;


    /**
     * Example of manual and intelligent schema control for this module.
     *
     * @return array<string, mixed>
     */
    protected function getSchemaFieldConfiguration(): array
    {
        return [
            'displayable' => [
                'id',
                'description',
                [
                    'name' => 'userGroups',
                    'type' => 'object',
                    'endpoint' => '/api/v1/user-groups',
                ],
            ],
            'creatable' => false,
            'editable' => false,
        ];
    }

    public function __construct(
        RoleResource $resource,
    ) {
        parent::__construct($resource);
    }
}
