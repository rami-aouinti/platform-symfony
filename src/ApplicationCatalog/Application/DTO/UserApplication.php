<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

final readonly class UserApplication
{
    public function __construct(
        private string $id,
        private string $applicationId,
        private string $applicationKeyName,
        private string $applicationName,
        private string $name,
        private ?string $logo,
        private ?string $description,
        private bool $active,
        private bool $owner,
    ) {
    }

    /**
     * @return array{id: string, applicationId: string, applicationKeyName: string, applicationName: string, name: string, logo: string|null, description: string|null, active: bool, owner: bool}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'applicationId' => $this->applicationId,
            'applicationKeyName' => $this->applicationKeyName,
            'applicationName' => $this->applicationName,
            'name' => $this->name,
            'logo' => $this->logo,
            'description' => $this->description,
            'active' => $this->active,
            'owner' => $this->owner,
        ];
    }
}
