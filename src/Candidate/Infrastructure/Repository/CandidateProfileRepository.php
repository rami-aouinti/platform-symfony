<?php

declare(strict_types=1);

namespace App\Candidate\Infrastructure\Repository;

use App\Candidate\Domain\Entity\CandidateProfile as Entity;
use App\Candidate\Domain\Repository\Interfaces\CandidateProfileRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class CandidateProfileRepository extends BaseRepository implements CandidateProfileRepositoryInterface
{
    protected static array $searchColumns = ['status'];
    protected static string $entityName = Entity::class;

    public function __construct(protected ManagerRegistry $managerRegistry)
    {
    }
}
