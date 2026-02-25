<?php

declare(strict_types=1);

namespace App\JobOffer\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\JobOffer\Domain\Entity\JobOffer as Entity;
use App\JobOffer\Domain\Repository\Interfaces\JobOfferRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class JobOfferRepository extends BaseRepository implements JobOfferRepositoryInterface
{
    protected static array $searchColumns = ['title', 'description', 'location', 'employmentType', 'status'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
