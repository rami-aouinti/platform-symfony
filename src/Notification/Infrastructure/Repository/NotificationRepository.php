<?php

declare(strict_types=1);

namespace App\Notification\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Notification\Domain\Entity\Notification as Entity;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface;
use App\User\Domain\Entity\User;
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

    public function findByUser(User $user, array $filters = []): array
    {
        $queryBuilder = $this->createQueryBuilder('notification')
            ->addSelect('user')
            ->innerJoin('notification.user', 'user')
            ->andWhere('notification.user = :user')
            ->setParameter('user', $user);

        if (isset($filters['read']) && $filters['read'] !== null) {
            $queryBuilder->andWhere(
                $filters['read'] ? 'notification.readAt IS NOT NULL' : 'notification.readAt IS NULL'
            );
        }

        if (isset($filters['type']) && $filters['type'] !== null) {
            $queryBuilder
                ->andWhere('notification.type = :type')
                ->setParameter('type', $filters['type']);
        }

        if (isset($filters['search']) && $filters['search'] !== null) {
            $queryBuilder
                ->andWhere('(notification.title LIKE :search OR notification.message LIKE :search)')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        $queryBuilder->orderBy('notification.createdAt', 'DESC');

        if (isset($filters['limit']) && $filters['limit'] !== null) {
            $queryBuilder->setMaxResults((int)$filters['limit']);
        }

        if (isset($filters['offset']) && $filters['offset'] !== null) {
            $queryBuilder->setFirstResult((int)$filters['offset']);
        }

        /** @var array<int, Entity> $notifications */
        $notifications = $queryBuilder->getQuery()->getResult();

        return $notifications;
    }

    public function findOneByIdAndUser(string $id, User $user): ?Entity
    {
        /** @var Entity|null $notification */
        $notification = $this->createQueryBuilder('notification')
            ->addSelect('owner')
            ->innerJoin('notification.user', 'owner')
            ->andWhere('notification.id = :id')
            ->andWhere('notification.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();

        return $notification;
    }

    public function markAllAsReadForUser(User $user): int
    {
        return $this->createQueryBuilder('notification')
            ->update()
            ->set('notification.readAt', ':readAt')
            ->andWhere('notification.user = :user')
            ->andWhere('notification.readAt IS NULL')
            ->setParameter('readAt', new \DateTime())
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function countUnreadForUser(User $user): int
    {
        return (int)$this->createQueryBuilder('notification')
            ->select('COUNT(notification.id)')
            ->andWhere('notification.user = :user')
            ->andWhere('notification.readAt IS NULL')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
