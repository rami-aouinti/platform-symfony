<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Anon;

use App\General\Transport\Rest\Traits\Methods\FindMethod;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * Trait to add 'findAction' for REST controllers for anonymous users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\FindMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Anon
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait FindAction
{
    use FindMethod;

    /**
     * Get list of entities, accessible for anonymous users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '',
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(summary: 'Endpoint find', description: 'Documentation standardisée de endpoint.', security: [['Bearer' => []], ['ApiKey' => []]])]
    #[OA\Parameter(name: 'where', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
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
    #[OA\Response(response: 400, ref: '#/components/responses/BadRequestError')]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    #[OA\Response(response: 404, ref: '#/components/responses/NotFoundError')]
    #[OA\Response(response: 422, ref: '#/components/responses/ValidationError')]
    public function findAction(Request $request): Response
    {
        return $this->findMethod($request);
    }
}
