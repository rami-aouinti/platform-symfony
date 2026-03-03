<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Infrastructure\Repository;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication as Entity;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class UserApplicationRepository extends BaseRepository implements UserApplicationRepositoryInterface
{
    protected static array $searchColumns = [];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByUserAndApplication(User $user, Application $application): ?Entity
    {
        return $this->findOneBy([
            'user' => $user,
            'application' => $application,
        ]);
    }
}
