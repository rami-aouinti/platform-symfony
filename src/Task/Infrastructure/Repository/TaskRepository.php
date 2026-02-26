<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Task\Domain\Entity\Task as Entity;
use App\Task\Domain\Repository\Interfaces\TaskRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    protected static array $searchColumns = ['title', 'description', 'status', 'priority'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
