<?php

declare(strict_types=1);

namespace App\Notification\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Notification\Domain\Entity\Notification as Entity;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    protected static array $searchColumns = ['title', 'message', 'type'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry
    ) {
    }
}
