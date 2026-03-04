<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

use App\ApplicationCatalog\Domain\Entity\UserApplication as UserApplicationEntity;
use App\User\Domain\Entity\User;

final class UserApplicationMapper
{
    public function mapEntityToDto(UserApplicationEntity $userApplication, ?User $currentUser = null): UserApplication
    {
        $application = $userApplication->getApplication();

        return new UserApplication(
            id: $userApplication->getId(),
            applicationId: $application->getId(),
            applicationKeyName: $application->getKeyName(),
            applicationName: $application->getName(),
            name: $userApplication->getName(),
            keyName: $userApplication->getKeyName(),
            logo: $userApplication->getLogo(),
            description: $userApplication->getDescription(),
            active: $userApplication->isActive(),
            public: $userApplication->isPublic(),
            owner: $currentUser instanceof User && $currentUser->getId() === $userApplication->getUser()->getId(),
        );
    }
}
