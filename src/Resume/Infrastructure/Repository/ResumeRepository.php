<?php

declare(strict_types=1);

namespace App\Resume\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Resume\Domain\Entity\Resume as Entity;
use App\Resume\Domain\Repository\Interfaces\ResumeRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class ResumeRepository extends BaseRepository implements ResumeRepositoryInterface
{
    protected static array $searchColumns = ['title', 'summary'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
