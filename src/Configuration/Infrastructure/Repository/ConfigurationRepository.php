<?php

declare(strict_types=1);

namespace App\Configuration\Infrastructure\Repository;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\Configuration\Domain\Entity\Configuration as Entity;
use App\Configuration\Domain\Repository\Interfaces\ConfigurationRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

use function is_string;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Configuration\Infrastructure\Repository
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class ConfigurationRepository extends BaseRepository implements ConfigurationRepositoryInterface
{
    protected static array $searchColumns = ['code', 'keyName', 'status'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findByUserApplication(UserApplication $userApplication): array
    {
        /** @var Entity[] $result */
        $result = $this->findBy([
            'userApplication' => $userApplication,
        ], [
            'keyName' => 'ASC',
        ]);

        return $result;
    }

    public function findByUserApplicationAndKeyName(UserApplication $userApplication, ?string $keyName = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('c')
            ->from(Entity::class, 'c')
            ->where('c.userApplication = :userApplication')
            ->setParameter('userApplication', $userApplication)
            ->orderBy('c.keyName', 'ASC');

        if (is_string($keyName) && $keyName !== '') {
            $qb
                ->andWhere('LOWER(c.keyName) LIKE :keyName')
                ->setParameter('keyName', '%' . mb_strtolower($keyName) . '%');
        }

        /** @var Entity[] $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function findOneByUserApplicationAndKeyName(UserApplication $userApplication, string $keyName): ?Entity
    {
        /** @var Entity|null $result */
        $result = $this->findOneBy([
            'userApplication' => $userApplication,
            'keyName' => $keyName,
        ]);

        return $result;
    }
}
