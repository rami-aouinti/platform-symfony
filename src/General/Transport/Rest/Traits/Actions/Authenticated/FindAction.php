<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Authenticated;

use App\General\Transport\Rest\Traits\Methods\FindMethod;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'findAction' for REST controllers for authenticated users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Authenticated
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait FindAction
{
    use FindMethod;

    /**
     * Get list of entities, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(
        summary: 'Lister les ressources',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Périmètre des données: collection filtrée via where/search/order, limitée aux données autorisées par le module.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\Parameter(name: 'where', in: 'query', required: false, description: 'Critères JSON. Exemple: {"status":"active","owner.id":"0195f7ac-199f-7188-bc2c-fe59f1161b08"}', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, description: 'Texte libre ou JSON and/or. Exemple: {"or":["backend","symfony"]}', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'order[createdAt]', in: 'query', required: false, description: 'Tri ASC|DESC', schema: new OA\Schema(type: 'string', example: 'DESC'))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 20))]
    #[OA\Parameter(name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 0))]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(type: 'string'),
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}
