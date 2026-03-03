<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource;

use App\ApplicationCatalog\Application\DTO\Application;
use App\ApplicationCatalog\Application\DTO\ApplicationMapper;
use App\ApplicationCatalog\Application\Resource\Interfaces\ApplicationListResourceInterface;
use App\ApplicationCatalog\Domain\Repository\Interfaces\ApplicationRepositoryInterface;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\User\Domain\Entity\User;

final readonly class ApplicationListResource implements ApplicationListResourceInterface
{
    public function __construct(
        private ApplicationRepositoryInterface $applicationRepository,
        private UserApplicationRepositoryInterface $userApplicationRepository,
        private ApplicationMapper $applicationMapper,
    ) {
    }

    public function listCatalog(): array
    {
        $items = [];

        foreach ($this->applicationRepository->findActiveOrderedByName() as $application) {
            $items[] = $this->applicationMapper->mapEntityToDto($application);
        }

        return $items;
    }

    public function listForUser(User $user): array
    {
        $indexedUserApplications = $this->userApplicationRepository->findByUserIndexedByApplicationId($user);
        $items = [];

        foreach ($this->applicationRepository->findActiveOrderedByName() as $application) {
            $items[] = $this->applicationMapper->mapEntityToDto(
                $application,
                $indexedUserApplications[$application->getId()] ?? null,
            );
        }

        return $items;
    }
}
