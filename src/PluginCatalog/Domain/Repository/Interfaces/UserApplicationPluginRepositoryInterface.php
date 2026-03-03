<?php

declare(strict_types=1);

namespace App\PluginCatalog\Domain\Repository\Interfaces;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\PluginCatalog\Domain\Entity\Plugin;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin;

interface UserApplicationPluginRepositoryInterface
{
    public function findOneByUserApplicationAndPlugin(UserApplication $userApplication, Plugin $plugin): ?UserApplicationPlugin;

    /**
     * @return UserApplicationPlugin[]
     */
    public function findByUserApplication(UserApplication $userApplication): array;

    /**
     * @return array<string, UserApplicationPlugin>
     */
    public function findByUserApplicationIndexedByPluginId(UserApplication $userApplication): array;

    public function save(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): BaseRepositoryInterface;

    public function remove(EntityInterface $entity, ?bool $flush = null, ?string $entityManagerName = null): BaseRepositoryInterface;
}
