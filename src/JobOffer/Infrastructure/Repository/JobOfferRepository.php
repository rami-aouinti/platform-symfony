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
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

use function assert;
use function usort;

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
        $queryBuilder = $this->createMyOffersQueryBuilder(
            $user,
            $hasGlobalManagePermission,
            $criteria,
            $search,
            $entityManagerName,
        );

        $this->applyOrderAndPagination($queryBuilder, $orderBy, $limit, $offset);

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
        $queryBuilder = $this->createAvailableOffersQueryBuilder(
            $user,
            $hasGlobalApplyPermission,
            $criteria,
            $search,
            $entityManagerName,
        );

        $this->applyOrderAndPagination($queryBuilder, $orderBy, $limit, $offset);

        return $this->executeQueryBuilder($queryBuilder);
    }

    public function computeFacets(
        ?array $criteria = null,
        ?array $search = null,
        ?array $postFilters = null,
        ?string $entityManagerName = null,
    ): array {
        $baseQueryBuilder = $this->createFacetBaseQueryBuilder(
            $criteria,
            $search,
            $postFilters ?? ['skills' => [], 'languages' => []],
            $entityManagerName,
        );

        return [
            'facets' => [
                [
                    'key' => 'skills',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchAssociationFacet(
                        clone $baseQueryBuilder,
                        'entity.skills',
                        'facetValue',
                        'facetValue.name',
                    ),
                ],
                [
                    'key' => 'languages',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchAssociationFacet(
                        clone $baseQueryBuilder,
                        'entity.languages',
                        'facetValue',
                        'facetValue.code',
                    ),
                ],
                [
                    'key' => 'cities',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchAssociationFacet(
                        clone $baseQueryBuilder,
                        'entity.city',
                        'facetValue',
                        'facetValue.name',
                        false,
                    ),
                ],
                [
                    'key' => 'regions',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchAssociationFacet(
                        clone $baseQueryBuilder,
                        'entity.region',
                        'facetValue',
                        'facetValue.name',
                        false,
                    ),
                ],
                [
                    'key' => 'jobCategories',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchAssociationFacet(
                        clone $baseQueryBuilder,
                        'entity.jobCategory',
                        'facetValue',
                        'facetValue.name',
                        false,
                    ),
                ],
                [
                    'key' => 'employmentTypes',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchScalarFacet(clone $baseQueryBuilder, 'entity.employmentType'),
                ],
                [
                    'key' => 'remotePolicies',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchScalarFacet(clone $baseQueryBuilder, 'entity.remoteMode', true),
                ],
                [
                    'key' => 'workTimes',
                    'sort' => 'count_desc,label_asc',
                    'values' => $this->fetchScalarFacet(clone $baseQueryBuilder, 'entity.workTime', true),
                ],
            ],
        ];
    }

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, array<int, string>>|null $search
     * @param array<string, string>|null $orderBy
     */
    private function createFilteredQueryBuilder(
        ?array $criteria,
        ?array $search,
        ?string $entityManagerName,
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder(entityManagerName: $entityManagerName);

        RepositoryHelper::processCriteria($queryBuilder, $criteria ?? []);
        RepositoryHelper::processSearchTerms($queryBuilder, $this->getSearchColumns(), $search ?? []);

        $queryBuilder->distinct();

        return $queryBuilder;
    }

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, array<int, string>>|null $search
     */
    private function createMyOffersQueryBuilder(
        User $user,
        bool $hasGlobalManagePermission,
        ?array $criteria,
        ?array $search,
        ?string $entityManagerName,
    ): QueryBuilder {
        $queryBuilder = $this->createFilteredQueryBuilder(
            $criteria,
            $search,
            $entityManagerName,
        );

        if ($hasGlobalManagePermission) {
            return $queryBuilder;
        }

        $queryBuilder
            ->leftJoin('entity.company', 'company')
            ->leftJoin(
                'company.memberships',
                'manageMembership',
                'WITH',
                'manageMembership.user = :manageUser AND manageMembership.role IN (:manageableRoles)',
            )
            ->andWhere('entity.createdBy = :manageUser OR manageMembership.id IS NOT NULL')
            ->setParameter('manageUser', $user)
            ->setParameter('manageableRoles', [
                CompanyMembership::ROLE_OWNER,
                CompanyMembership::ROLE_CRM_MANAGER,
            ]);

        return $queryBuilder;
    }

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, array<int, string>>|null $search
     */
    private function createAvailableOffersQueryBuilder(
        User $user,
        bool $hasGlobalApplyPermission,
        ?array $criteria,
        ?array $search,
        ?string $entityManagerName,
    ): QueryBuilder {
        $queryBuilder = $this->createFilteredQueryBuilder(
            $criteria,
            $search,
            $entityManagerName,
        );

        $queryBuilder
            ->andWhere('entity.status = :statusOpen')
            ->setParameter('statusOpen', 'open');

        if ($hasGlobalApplyPermission) {
            return $queryBuilder;
        }

        $queryBuilder
            ->leftJoin('entity.company', 'availableCompany')
            ->leftJoin(
                'availableCompany.memberships',
                'applyMembership',
                'WITH',
                'applyMembership.user = :applyUser AND applyMembership.role = :candidateRole',
            )
            ->andWhere('applyMembership.id IS NOT NULL')
            ->setParameter('applyUser', $user)
            ->setParameter('candidateRole', CompanyMembership::ROLE_CANDIDATE);

        return $queryBuilder;
    }

    /**
     * @param array<string, string>|null $orderBy
     */
    private function applyOrderAndPagination(
        QueryBuilder $queryBuilder,
        ?array $orderBy,
        ?int $limit,
        ?int $offset,
    ): void {
        RepositoryHelper::processOrderBy($queryBuilder, $orderBy ?? []);

        $queryBuilder
            ->setMaxResults($limit)
            ->setFirstResult($offset ?? 0);

    }

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, array<int, string>>|null $search
     * @param array{skills: array<int, string>, languages: array<int, string>} $postFilters
     */
    private function createFacetBaseQueryBuilder(
        ?array $criteria,
        ?array $search,
        array $postFilters,
        ?string $entityManagerName,
    ): QueryBuilder {
        $queryBuilder = $this->createQueryBuilder(entityManagerName: $entityManagerName);

        RepositoryHelper::processCriteria($queryBuilder, $criteria ?? []);
        RepositoryHelper::processSearchTerms($queryBuilder, $this->getSearchColumns(), $search ?? []);

        if ($postFilters['skills'] !== []) {
            $queryBuilder
                ->innerJoin('entity.skills', 'filterSkill')
                ->andWhere('filterSkill.id IN (:facetFilterSkills)')
                ->setParameter('facetFilterSkills', $postFilters['skills']);
        }

        if ($postFilters['languages'] !== []) {
            $queryBuilder
                ->innerJoin('entity.languages', 'filterLanguage')
                ->andWhere('filterLanguage.id IN (:facetFilterLanguages)')
                ->setParameter('facetFilterLanguages', $postFilters['languages']);
        }

        $queryBuilder
            ->setFirstResult(null)
            ->setMaxResults(null)
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

    /**
     * @return array<int, array{id: string, label: string, count: int}>
     */
    private function fetchAssociationFacet(
        QueryBuilder $queryBuilder,
        string $join,
        string $alias,
        string $labelField,
        bool $isManyToMany = true,
    ): array {
        $joinMethod = $isManyToMany ? 'innerJoin' : 'leftJoin';

        $queryBuilder->{$joinMethod}($join, $alias)
            ->andWhere($alias . '.id IS NOT NULL')
            ->select($alias . '.id AS id', $labelField . ' AS label', 'COUNT(DISTINCT entity.id) AS count')
            ->groupBy($alias . '.id')
            ->addGroupBy($labelField)
            ->orderBy('count', 'DESC')
            ->addOrderBy('label', 'ASC');

        return $this->executeFacetAggregation($queryBuilder);
    }

    /**
     * @return array<int, array{id: string, label: string, count: int}>
     */
    private function fetchScalarFacet(QueryBuilder $queryBuilder, string $field, bool $excludeNull = false): array
    {
        if ($excludeNull) {
            $queryBuilder->andWhere($field . ' IS NOT NULL');
        }

        $queryBuilder
            ->select($field . ' AS id', $field . ' AS label', 'COUNT(DISTINCT entity.id) AS count')
            ->groupBy($field)
            ->orderBy('count', 'DESC')
            ->addOrderBy('label', 'ASC');

        return $this->executeFacetAggregation($queryBuilder);
    }

    /**
     * @return array<int, array{id: string, label: string, count: int}>
     */
    private function executeFacetAggregation(QueryBuilder $queryBuilder): array
    {
        $this->processQueryBuilder($queryBuilder);
        $result = $queryBuilder->getQuery()->getArrayResult();
        RepositoryHelper::resetParameterCount();

        $rows = [];

        foreach ($result as $row) {
            $rows[] = [
                'id' => (string) $row['id'],
                'label' => (string) $row['label'],
                'count' => (int) $row['count'],
            ];
        }

        usort(
            $rows,
            static fn (array $left, array $right): int => $right['count'] <=> $left['count'] ?: $left['label'] <=> $right['label'],
        );

        return $rows;
    }
}
