<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Page\Domain\Entity\About as Entity;
use App\Page\Domain\Repository\Interfaces\AboutRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class AboutRepository extends BaseRepository implements AboutRepositoryInterface
{
    protected static array $searchColumns = ['name', 'description'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
