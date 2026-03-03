<?php

declare(strict_types=1);

namespace App\PluginCatalog\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\PluginCatalog\Domain\Entity\Plugin as Entity;
use App\PluginCatalog\Domain\Repository\Interfaces\PluginRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class PluginRepository extends BaseRepository implements PluginRepositoryInterface
{
    protected static array $searchColumns = [];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findActiveOrderedByName(): array
    {
        return $this->findBy([
            'active' => true,
        ], [
            'name' => 'ASC',
        ]);
    }
}
