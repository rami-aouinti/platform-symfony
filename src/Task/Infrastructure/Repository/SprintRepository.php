<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Task\Domain\Entity\Sprint as Entity;
use App\Task\Domain\Repository\Interfaces\SprintRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class SprintRepository extends BaseRepository implements SprintRepositoryInterface
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
