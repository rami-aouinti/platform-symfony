<?php

declare(strict_types=1);

namespace App\PluginCatalog\Infrastructure\Repository;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\General\Infrastructure\Repository\BaseRepository;
use App\PluginCatalog\Domain\Entity\Plugin;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin as Entity;
use App\PluginCatalog\Domain\Repository\Interfaces\UserApplicationPluginRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class UserApplicationPluginRepository extends BaseRepository implements UserApplicationPluginRepositoryInterface
{
    protected static array $searchColumns = [];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByUserApplicationAndPlugin(UserApplication $userApplication, Plugin $plugin): ?Entity
    {
        return $this->findOneBy([
            'userApplication' => $userApplication,
            'plugin' => $plugin,
        ]);
    }

    public function findByUserApplication(UserApplication $userApplication): array
    {
        return $this->findBy(['userApplication' => $userApplication]);
    }

    public function findByUserApplicationIndexedByPluginId(UserApplication $userApplication): array
    {
        $items = [];

        foreach ($this->findByUserApplication($userApplication) as $userApplicationPlugin) {
            $items[$userApplicationPlugin->getPlugin()->getId()] = $userApplicationPlugin;
        }

        return $items;
    }
}
