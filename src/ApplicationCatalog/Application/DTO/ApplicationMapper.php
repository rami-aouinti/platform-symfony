<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

use App\ApplicationCatalog\Domain\Entity\Application as ApplicationEntity;
use App\ApplicationCatalog\Domain\Entity\UserApplication;

final class ApplicationMapper
{
    public function mapEntityToDto(ApplicationEntity $application, ?UserApplication $userApplication = null): Application
    {
        return new Application(
            id: $application->getId(),
            userApplicationId: $userApplication?->getId(),
            keyName: $application->getKeyName(),
            name: $application->getName(),
            logo: $application->getLogo(),
            description: $application->getDescription(),
            active: $application->isActive(),
            enabled: $userApplication?->isActive(),
        );
    }
}
