<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RestResourceSchemaRelation',
    type: 'object',
    properties: [
        new OA\Property(property: 'type', type: 'string', example: 'manyToOne'),
        new OA\Property(property: 'targetEntity', type: 'string', example: 'App\\Task\\Domain\\Entity\\Project'),
    ],
)]
#[OA\Schema(
    schema: 'RestResourceSchemaResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'displayable', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(property: 'editable', type: 'array', items: new OA\Items(type: 'string')),
        new OA\Property(
            property: 'relations',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(ref: '#/components/schemas/RestResourceSchemaRelation'),
        ),
    ],
    example: [
        'displayable' => ['id', 'title', 'status'],
        'editable' => ['title', 'status', 'project'],
        'relations' => [
            'project' => [
                'type' => 'manyToOne',
                'targetEntity' => 'App\\Task\\Domain\\Entity\\Project',
            ],
        ],
    ],
)]
final class SchemaResponse
{
}
