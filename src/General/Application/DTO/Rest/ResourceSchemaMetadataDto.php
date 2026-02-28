<?php

declare(strict_types=1);

namespace App\General\Application\DTO\Rest;

final readonly class ResourceSchemaMetadataDto
{
    /**
     * @param array<int, ResourceSchemaFieldDto> $displayable
     * @param array<int, ResourceSchemaFieldDto> $editable
     */
    public function __construct(
        public array $displayable,
        public array $editable,
    ) {
    }
}
