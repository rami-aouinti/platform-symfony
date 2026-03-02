<?php

declare(strict_types=1);

namespace App\Friend\Infrastructure\Repository;

use App\Friend\Domain\Entity\FriendRequest;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;

class FriendRequestRepository extends BaseRepository
{
    protected static string $entityName = FriendRequest::class;

    public function __construct(protected ManagerRegistry $managerRegistry)
    {
    }

    public function findBetweenUsers(string $firstUserId, string $secondUserId): ?FriendRequest
    {
        return $this->createQueryBuilder('friendRequest')
            ->where('(friendRequest.requester = :firstUserId AND friendRequest.addressee = :secondUserId) OR (friendRequest.requester = :secondUserId AND friendRequest.addressee = :firstUserId)')
            ->setParameter('firstUserId', $firstUserId, UuidBinaryOrderedTimeType::NAME)
            ->setParameter('secondUserId', $secondUserId, UuidBinaryOrderedTimeType::NAME)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return array<int, FriendRequest> */
    public function findAcceptedByUser(string $userId): array
    {
        return $this->createQueryBuilder('friendRequest')
            ->where('(friendRequest.requester = :userId OR friendRequest.addressee = :userId)')
            ->andWhere('friendRequest.status = :status')
            ->setParameter('userId', $userId, UuidBinaryOrderedTimeType::NAME)
            ->setParameter('status', FriendRequest::STATUS_ACCEPTED)
            ->orderBy('friendRequest.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<int, FriendRequest> */
    public function findPendingReceivedByUser(string $userId): array
    {
        return $this->createQueryBuilder('friendRequest')
            ->where('friendRequest.addressee = :userId')
            ->andWhere('friendRequest.status = :status')
            ->setParameter('userId', $userId, UuidBinaryOrderedTimeType::NAME)
            ->setParameter('status', FriendRequest::STATUS_PENDING)
            ->orderBy('friendRequest.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** @return array<int, FriendRequest> */
    public function findPendingSentByUser(string $userId): array
    {
        return $this->createQueryBuilder('friendRequest')
            ->where('friendRequest.requester = :userId')
            ->andWhere('friendRequest.status = :status')
            ->setParameter('userId', $userId, UuidBinaryOrderedTimeType::NAME)
            ->setParameter('status', FriendRequest::STATUS_PENDING)
            ->orderBy('friendRequest.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
