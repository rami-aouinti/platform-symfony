<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\Repository;

use App\Company\Domain\Entity\Company as Entity;
use App\Company\Domain\Repository\Interfaces\CompanyRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    protected static array $searchColumns = ['legalName', 'slug', 'status'];
    protected static string $entityName = Entity::class;

    public function __construct(protected ManagerRegistry $managerRegistry)
    {
    }
}
