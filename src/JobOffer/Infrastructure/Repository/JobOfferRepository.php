<?php

declare(strict_types=1);

namespace App\JobOffer\Infrastructure\Repository;

use ArrayIterator;
use App\Company\Domain\Entity\CompanyMembership;
use App\General\Infrastructure\Rest\RepositoryHelper;
use App\General\Infrastructure\Repository\BaseRepository;
use App\JobOffer\Domain\Entity\JobOffer as Entity;
use App\JobOffer\Domain\Repository\Interfaces\JobOfferRepositoryInterface;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

use function assert;

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

    public function findMyOffersQuery(
        User $user,
        bool $hasGlobalManagePermission,
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array {
        $queryBuilder = $this->createFilteredQueryBuilder(
            $criteria,
            $search,
            $orderBy,
            $limit,
            $offset,
            $entityManagerName,
        );

        if (!$hasGlobalManagePermission) {
            $queryBuilder
                ->leftJoin('entity.company', 'company')
                ->leftJoin(
                    'company.memberships',
                    'manageMembership',
                    'WITH',
                    'manageMembership.user = :user AND manageMembership.role IN (:manageableRoles)',
                )
                ->andWhere('entity.createdBy = :user OR manageMembership.id IS NOT NULL')
                ->setParameter('user', $user)
                ->setParameter('manageableRoles', [
                    CompanyMembership::ROLE_OWNER,
                    CompanyMembership::ROLE_CRM_MANAGER,
                ]);
        }

        return $this->executeQueryBuilder($queryBuilder);
    }

    public function findAvailableOffersQuery(
        User $user,
        bool $hasGlobalApplyPermission,
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array {
        $queryBuilder = $this->createFilteredQueryBuilder(
            $criteria,
            $search,
            $orderBy,
            $limit,
            $offset,
            $entityManagerName,
        );

        $queryBuilder
            ->andWhere('entity.status = :statusOpen')
            ->setParameter('statusOpen', 'open');

        if (!$hasGlobalApplyPermission) {
            $queryBuilder
                ->leftJoin('entity.company', 'company')
                ->leftJoin(
                    'company.memberships',
                    'applyMembership',
                    'WITH',
                    'applyMembership.user = :user AND applyMembership.role = :candidateRole',
                )
                ->andWhere('applyMembership.id IS NOT NULL')
                ->setParameter('user', $user)
                ->setParameter('candidateRole', CompanyMembership::ROLE_CANDIDATE);
        }

        return $this->executeQueryBuilder($queryBuilder);
    }

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, array<int, string>>|null $search
     * @param array<string, string>|null $orderBy
     */
    private function createFilteredQueryBuilder(
        ?array $criteria,
        ?array $search,
        ?array $orderBy,
        ?int $limit,
        ?int $offset,
        ?string $entityManagerName,
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder(entityManagerName: $entityManagerName);

        RepositoryHelper::processCriteria($queryBuilder, $criteria ?? []);
        RepositoryHelper::processSearchTerms($queryBuilder, $this->getSearchColumns(), $search ?? []);
        RepositoryHelper::processOrderBy($queryBuilder, $orderBy ?? []);

        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset ?? 0)
            ->distinct();

        return $queryBuilder;
    }

    /**
     * @return array<int, Entity>
     */
    private function executeQueryBuilder(QueryBuilder $queryBuilder): array
    {
        $this->processQueryBuilder($queryBuilder);
        RepositoryHelper::resetParameterCount();

        $iterator = new Paginator($queryBuilder, true)->getIterator();

        assert($iterator instanceof ArrayIterator);

        return $iterator->getArrayCopy();
    }
}
