<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RestResourceSchemaField',
    type: 'object',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'project'),
        new OA\Property(property: 'type', type: 'string', example: 'object', enum: ['normal', 'boolean', 'object']),
        new OA\Property(property: 'targetClass', type: 'string', nullable: true, example: 'App\\Task\\Domain\\Entity\\Project'),
        new OA\Property(property: 'endpoint', type: 'string', nullable: true, example: '/api/v1/projects'),
    ],
)]
#[OA\Schema(
    schema: 'RestResourceSchemaResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'displayable', type: 'array', items: new OA\Items(ref: '#/components/schemas/RestResourceSchemaField')),
        new OA\Property(property: 'editable', type: 'array', items: new OA\Items(ref: '#/components/schemas/RestResourceSchemaField')),
    ],
    example: [
        'displayable' => [
            ['name' => 'id', 'type' => 'normal'],
            ['name' => 'title', 'type' => 'normal'],
            ['name' => 'status', 'type' => 'normal'],
            ['name' => 'project', 'type' => 'object', 'targetClass' => 'App\\Task\\Domain\\Entity\\Project', 'endpoint' => '/api/v1/projects'],
        ],
        'editable' => [
            ['name' => 'title', 'type' => 'normal'],
            ['name' => 'status', 'type' => 'normal'],
            ['name' => 'project', 'type' => 'object', 'targetClass' => 'App\\Task\\Domain\\Entity\\Project', 'endpoint' => '/api/v1/projects'],
        ],
    ],
)]
final class SchemaResponse
{
}
