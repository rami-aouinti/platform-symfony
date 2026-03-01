<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Admin;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'patchAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\PatchMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Admin
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait PatchAction
{
    use PatchMethod;

    /**
     * Patch entity with new data, accessible only for 'ROLE_ADMIN' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/{id}',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_PATCH],
    )]
    #[IsGranted(Role::ADMIN->value)]
    #[OA\Patch(
        summary: 'Modifier partiellement une ressource',
        description: 'Audience cible: administrateurs. Rôle minimal: ROLE_ADMIN. Périmètre des données: mise à jour partielle des champs autorisés de la ressource.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\RequestBody(
        request: 'body',
        description: 'Exemple de payload de mise à jour partielle',
        content: new JsonContent(
            type: 'object',
            example: [
                'description' => 'Valeur partiellement mise à jour',
            ],
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(
            type: 'object',
            example: [],
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    #[OA\Response(response: 404, ref: '#/components/responses/NotFoundError')]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }
}
