<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Authenticated;

use App\General\Transport\Rest\Traits\Methods\DeleteMethod;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'deleteAction' for REST controllers for authenticated users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\DeleteMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Authenticated
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait DeleteAction
{
    use DeleteMethod;

    /**
     * Delete entity, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/{id}',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_DELETE],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Delete(
        summary: 'Supprimer une ressource',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Périmètre des données: suppression d’une ressource si autorisée par les règles métier.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'deleted',
        content: new JsonContent(
            type: 'object',
            example: [],
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    #[OA\Response(response: 404, ref: '#/components/responses/NotFoundError')]
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
