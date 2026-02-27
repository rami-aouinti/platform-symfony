<?php

declare(strict_types=1);

namespace App\General\Application\DTO\Rest;

final readonly class ResourceSchemaRelationDto
{
    public function __construct(
        public string $targetClass,
        public string $cardinality,
    ) {
    }
}
