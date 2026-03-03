<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Domain\Repository\Interfaces;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\User\Domain\Entity\User;

interface UserApplicationRepositoryInterface
{
    public function findOneByUserAndApplication(User $user, Application $application): ?UserApplication;

    /**
     * @return UserApplication[]
     */
    public function findByUser(User $user): array;

    /**
     * @return array<string, UserApplication>
     */
    public function findByUserIndexedByApplicationId(User $user): array;

    public function save(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): BaseRepositoryInterface;

    public function remove(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): BaseRepositoryInterface;
}
