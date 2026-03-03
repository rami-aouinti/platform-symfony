<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Infrastructure\Repository;

use App\ApplicationCatalog\Domain\Entity\Application as Entity;
use App\ApplicationCatalog\Domain\Repository\Interfaces\ApplicationRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class ApplicationRepository extends BaseRepository implements ApplicationRepositoryInterface
{
    protected static array $searchColumns = ['name'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByName(string $name): ?Entity
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findAllOrderedByName(): array
    {
        return $this->findBy([], ['name' => 'ASC']);
    }

    public function findActiveOrderedByName(): array
    {
        return $this->findBy(['active' => true], ['name' => 'ASC']);
    }
}
