<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Admin;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'createAction' for REST controllers for 'ROLE_ADMIN' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Admin
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait CreateAction
{
    use CreateMethod;

    /**
     * Create entity, accessible only for 'ROLE_ADMIN' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '',
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(Role::ADMIN->value)]
    #[OA\Post(
        summary: 'Créer une ressource',
        description: 'Audience cible: administrateurs. Rôle minimal: ROLE_ADMIN. Périmètre des données: création d’une ressource dans le module sur les champs autorisés.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\RequestBody(
        request: 'body',
        description: 'Exemple de payload de création',
        content: new JsonContent(
            type: 'object',
            example: [
                'name' => 'Example name',
                'description' => 'Description initiale',
            ],
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'created',
        content: new JsonContent(
            type: 'object',
            example: [],
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
