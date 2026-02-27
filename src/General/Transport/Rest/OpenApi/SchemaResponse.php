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
    schema: 'RestResourceSchemaCreateSection',
    type: 'object',
    properties: [
        new OA\Property(property: 'fields', type: 'array', items: new OA\Items(ref: '#/components/schemas/RestResourceSchemaField')),
        new OA\Property(property: 'required', type: 'array', items: new OA\Items(type: 'string')),
    ],
)]
#[OA\Schema(
    schema: 'RestResourceSchemaResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'displayable', oneOf: [new OA\Schema(type: 'boolean', enum: [false]), new OA\Schema(type: 'array', items: new OA\Items(ref: '#/components/schemas/RestResourceSchemaField'))]),
        new OA\Property(property: 'editable', oneOf: [new OA\Schema(type: 'boolean', enum: [false]), new OA\Schema(type: 'array', items: new OA\Items(ref: '#/components/schemas/RestResourceSchemaField'))]),
        new OA\Property(property: 'creatable', oneOf: [new OA\Schema(type: 'boolean', enum: [false]), new OA\Schema(ref: '#/components/schemas/RestResourceSchemaCreateSection')]),
    ],
    example: [
        'displayable' => [
            ['name' => 'id', 'type' => 'normal', 'targetClass' => null, 'endpoint' => null],
            ['name' => 'title', 'type' => 'normal', 'targetClass' => null, 'endpoint' => null],
            ['name' => 'project', 'type' => 'object', 'targetClass' => 'App\\Task\\Domain\\Entity\\Project', 'endpoint' => '/api/v1/projects'],
        ],
        'editable' => [
            ['name' => 'title', 'type' => 'normal', 'targetClass' => null, 'endpoint' => null],
            ['name' => 'project', 'type' => 'object', 'targetClass' => 'App\\Task\\Domain\\Entity\\Project', 'endpoint' => '/api/v1/projects'],
        ],
        'creatable' => [
            'fields' => [
                ['name' => 'title', 'type' => 'normal', 'targetClass' => null, 'endpoint' => null],
                ['name' => 'project', 'type' => 'object', 'targetClass' => 'App\\Task\\Domain\\Entity\\Project', 'endpoint' => '/api/v1/projects'],
            ],
            'required' => ['title'],
        ],
    ],
)]
final class SchemaResponse
{
}
