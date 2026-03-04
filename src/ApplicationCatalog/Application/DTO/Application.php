<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

final readonly class Application
{
    public function __construct(
        private string $id,
        private ?string $userApplicationId,
        private string $name,
        private ?string $logo,
        private ?string $description,
        private bool $active,
        private ?bool $enabled,
    ) {
    }

    /**
     * @return array{id: string, userApplicationId: string|null, name: string, logo: string|null, description: string|null, active: bool, enabled: bool|null}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userApplicationId' => $this->userApplicationId,
            'name' => $this->name,
            'logo' => $this->logo,
            'description' => $this->description,
            'active' => $this->active,
            'enabled' => $this->enabled,
        ];
    }
}
