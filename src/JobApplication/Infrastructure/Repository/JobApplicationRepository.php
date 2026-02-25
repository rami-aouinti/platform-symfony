<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\JobApplication\Domain\Entity\JobApplication as Entity;
use App\JobApplication\Domain\Repository\Interfaces\JobApplicationRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class JobApplicationRepository extends BaseRepository implements JobApplicationRepositoryInterface
{
    protected static array $searchColumns = ['status'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
