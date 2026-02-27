<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Logged;

use App\General\Transport\Rest\Traits\Methods\SchemaMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

trait SchemaAction
{
    use SchemaMethod;

    /**
     * @throws Throwable
     */
    #[Route(path: '/schema', methods: [Request::METHOD_GET])]
    #[IsGranted(Role::LOGGED->value)]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(ref: '#/components/schemas/RestResourceSchemaResponse'),
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
    public function schemaAction(Request $request): Response
    {
        return $this->schemaMethod($request);
    }
}
