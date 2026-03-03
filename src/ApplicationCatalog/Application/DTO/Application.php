<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

final readonly class Application
{
    public function __construct(
        private string $id,
        private string $name,
        private ?string $logo,
        private bool $active,
        private ?bool $enabled,
    ) {
    }

    /**
     * @return array{id: string, name: string, logo: string|null, active: bool, enabled: bool|null}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo' => $this->logo,
            'active' => $this->active,
            'enabled' => $this->enabled,
        ];
    }
}
