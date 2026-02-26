<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Repository;

use App\Chat\Domain\Entity\ConversationParticipant as Entity;
use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class ConversationParticipantRepository extends BaseRepository implements ConversationParticipantRepositoryInterface
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findByConversationId(string $conversationId): array
    {
        return $this->createQueryBuilder('participant')
            ->join('participant.conversation', 'conversation')
            ->addSelect('user')
            ->join('participant.user', 'user')
            ->where('conversation.id = :conversationId')
            ->setParameter('conversationId', $conversationId)
            ->orderBy('participant.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
