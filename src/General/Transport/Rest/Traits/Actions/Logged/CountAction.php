<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Logged;

use App\General\Transport\Rest\Traits\Methods\CountMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait to add 'countAction' for REST controllers for 'ROLE_LOGGED' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\CountMethod for detailed documents.
 *
 * @package App\General\Transport\Rest\Traits\Actions\Logged
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
trait CountAction
{
    use CountMethod;

    /**
     * Count entities, accessible only for 'ROLE_LOGGED' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '/count',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(Role::LOGGED->value)]
    #[OA\Get(summary: 'Endpoint count', description: 'Documentation standardisée de endpoint.', security: [['Bearer' => []], ['ApiKey' => []]])]
    #[OA\Parameter(name: 'where', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 20))]
    #[OA\Parameter(name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 0))]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(
            properties: [
                new Property(property: 'count', type: 'integer'),
            ],
            type: 'object',
            example: [
                'count' => 1,
            ],
        ),
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied',
        content: new JsonContent(
            properties: [
                new Property(property: 'code', description: 'Error code', type: 'integer'),
                new Property(property: 'message', description: 'Error description', type: 'string'),
            ],
            type: 'object',
            example: [
                'code' => 403,
                'message' => 'Access denied',
            ],
        ),
    )]
    #[OA\Response(response: 400, ref: '#/components/responses/BadRequestError')]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 404, ref: '#/components/responses/NotFoundError')]
    #[OA\Response(response: 422, ref: '#/components/responses/ValidationError')]
    public function countAction(Request $request): Response
    {
        return $this->countMethod($request);
    }
}
