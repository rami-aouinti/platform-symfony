<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\Repository;

use App\Chat\Domain\Entity\Conversation as Entity;
use App\Chat\Domain\Repository\Interfaces\ConversationRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Chat
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ConversationRepository extends BaseRepository implements ConversationRepositoryInterface
{
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByJobApplicationId(string $jobApplicationId): ?Entity
    {
        return $this->createQueryBuilder('conversation')
            ->join('conversation.jobApplication', 'jobApplication')
            ->where('jobApplication.id = :jobApplicationId')
            ->setParameter('jobApplicationId', $jobApplicationId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
