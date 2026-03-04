<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource;

use App\ApplicationCatalog\Application\DTO\UserApplication;
use App\ApplicationCatalog\Application\DTO\UserApplicationMapper;
use App\ApplicationCatalog\Application\Resource\Interfaces\PublicUserApplicationListResourceInterface;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\User\Domain\Entity\User;

final readonly class PublicUserApplicationListResource implements PublicUserApplicationListResourceInterface
{
    public function __construct(
        private UserApplicationRepositoryInterface $userApplicationRepository,
        private UserApplicationMapper $userApplicationMapper,
    ) {
    }

    public function list(?User $currentUser = null): array
    {
        $items = [];

        foreach ($this->userApplicationRepository->findAllOrderedByCreatedAt() as $userApplication) {
            $items[] = $this->userApplicationMapper->mapEntityToDto($userApplication, $currentUser);
        }

        return $items;
    }
}
