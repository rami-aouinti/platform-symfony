<?php

declare(strict_types=1);

namespace App\General\Application\DTO\Rest;

final readonly class ResourceSchemaMetadataDto
{
    /**
     * @param array<int, string> $displayable
     * @param array<int, string> $editable
     * @param array<string, ResourceSchemaRelationDto> $relations
     */
    public function __construct(
        public array $displayable,
        public array $editable,
        public array $relations,
    ) {
    }
}
