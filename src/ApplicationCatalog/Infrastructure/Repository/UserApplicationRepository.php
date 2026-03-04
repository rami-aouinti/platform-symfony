<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Infrastructure\Repository;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication as Entity;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\General\Domain\Rest\UuidHelper;
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
        $items = $this->findByUserAndApplication($user, $application);

        return $items[0] ?? null;
    }

    public function findByUserAndApplication(User $user, Application $application): array
    {
        /** @var Entity|null $userApplication */
        $userApplications = $this
            ->createQueryBuilder()
            ->where('IDENTITY(entity.user) = :userId')
            ->andWhere('IDENTITY(entity.application) = :applicationId')
            ->setParameter('userId', $user->getId(), UuidHelper::getType($user->getId()))
            ->setParameter('applicationId', $application->getId(), UuidHelper::getType($application->getId()))
            ->getQuery()
            ->getResult();

        return $userApplications;
    }

    public function findByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    public function findAllOrderedByCreatedAt(): array
    {
        return $this->findBy([], ['createdAt' => 'DESC']);
    }

    public function findByUserIndexedByApplicationId(User $user): array
    {
        $items = [];

        foreach ($this->findByUser($user) as $userApplication) {
            $items[$userApplication->getApplication()->getId()] = $userApplication;
        }

        return $items;
    }
}
