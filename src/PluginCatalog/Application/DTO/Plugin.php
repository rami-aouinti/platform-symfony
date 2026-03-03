<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\DTO;

final readonly class Plugin
{
    public function __construct(
        private string $id,
        private string $keyName,
        private string $name,
        private ?string $logo,
        private ?string $description,
        private bool $active,
        private ?bool $enabled,
    ) {
    }

    /**
     * @return array{id: string, keyName: string, name: string, logo: string|null, description: string|null, active: bool, enabled: bool|null}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'keyName' => $this->keyName,
            'name' => $this->name,
            'logo' => $this->logo,
            'description' => $this->description,
            'active' => $this->active,
            'enabled' => $this->enabled,
        ];
    }
}
