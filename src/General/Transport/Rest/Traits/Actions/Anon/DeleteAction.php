<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Anon;

use App\General\Transport\Rest\Traits\Methods\DeleteMethod;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Throwable;

/**
 * Trait to add 'deleteAction' for REST controllers for anonymous users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\DeleteMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Anon
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait DeleteAction
{
    use DeleteMethod;

    /**
     * Delete entity, accessible for anonymous users.
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
    #[OA\Delete(summary: 'Endpoint delete', description: 'Documentation standardisée de endpoint.', security: [['Bearer' => []], ['ApiKey' => []]])]
    #[OA\Response(
        response: 200,
        description: 'deleted',
        content: new JsonContent(
            type: 'object',
            example: [],
        ),
    )]
    #[OA\Response(response: 400, ref: '#/components/responses/BadRequestError')]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    #[OA\Response(response: 404, ref: '#/components/responses/NotFoundError')]
    #[OA\Response(response: 422, ref: '#/components/responses/ValidationError')]
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
