<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserProfile as Entity;
use App\User\Domain\Repository\Interfaces\UserProfileRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @package App\User
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findAdvanced(string $id, string|int|null $hydrationMode = null, string|null $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @method Entity[] findAll(?string $entityManagerName = null)
 *
 * @codingStandardsIgnoreEnd
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class UserProfileRepository extends BaseRepository implements UserProfileRepositoryInterface
{
    /**
     * @psalm-var class-string
     */
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByUser(User $user): ?Entity
    {
        /** @var Entity|null $profile */
        $profile = $this->findOneBy([
            'user' => $user,
        ]);

        return $profile;
    }
}
