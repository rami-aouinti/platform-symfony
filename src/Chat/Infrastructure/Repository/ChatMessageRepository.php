<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Repository;

use App\Chat\Domain\Entity\ChatMessage as Entity;
use App\Chat\Domain\Repository\Interfaces\ChatMessageRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class ChatMessageRepository extends BaseRepository implements ChatMessageRepositoryInterface
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findById(string $id): ?Entity
    {
        /** @var Entity|null $message */
        $message = parent::find($id);

        return $message;
    }

    public function findByConversationId(string $conversationId): array
    {
        return $this->createQueryBuilder('message')
            ->join('message.conversation', 'conversation')
            ->addSelect('sender')
            ->join('message.sender', 'sender')
            ->where('conversation.id = :conversationId')
            ->setParameter('conversationId', $conversationId)
            ->orderBy('message.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
