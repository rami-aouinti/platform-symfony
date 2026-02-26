<?php

declare(strict_types=1);

namespace App\Configuration\Infrastructure\Repository;

use App\Configuration\Domain\Entity\Configuration as Entity;
use App\Configuration\Domain\Repository\Interfaces\ConfigurationRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class ConfigurationRepository extends BaseRepository implements ConfigurationRepositoryInterface
{
    protected static array $searchColumns = ['code', 'keyName', 'value', 'status'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
