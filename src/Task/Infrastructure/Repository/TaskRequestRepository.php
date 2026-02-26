<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Task\Domain\Entity\TaskRequest as Entity;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class TaskRequestRepository extends BaseRepository implements TaskRequestRepositoryInterface
{
    protected static array $searchColumns = ['status', 'type', 'note'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
