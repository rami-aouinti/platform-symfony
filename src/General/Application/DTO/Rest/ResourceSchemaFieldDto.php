<?php

declare(strict_types=1);

namespace App\General\Application\DTO\Rest;

final readonly class ResourceSchemaFieldDto
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $targetClass = null,
        public ?string $endpoint = null,
    ) {
    }
}
