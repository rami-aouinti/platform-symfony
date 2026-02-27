<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Anon;

use App\General\Transport\Rest\Traits\Methods\SchemaMethod;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

trait SchemaAction
{
    use SchemaMethod;

    /**
     * @throws Throwable
     */
    #[Route(path: '/schema', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'success',
        content: new JsonContent(ref: '#/components/schemas/RestResourceSchemaResponse'),
    )]
    public function schemaAction(Request $request): Response
    {
        return $this->schemaMethod($request);
    }
}
