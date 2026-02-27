<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Repository\Interfaces;

use App\Recruit\Domain\Entity\JobOffer;
use App\User\Domain\Entity\User;

/**
 * @package App\Recruit\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface JobOfferRepositoryInterface
{
    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, string>|null $orderBy
     * @param array<string, array<int, string>>|null $search
     *
     * @return array<int, JobOffer>
     */
    public function findMyOffersQuery(
        User $user,
        bool $hasGlobalManagePermission,
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array;

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, string>|null $orderBy
     * @param array<string, array<int, string>>|null $search
     *
     * @return array<int, JobOffer>
     */
    public function findAvailableOffersQuery(
        User $user,
        bool $hasGlobalApplyPermission,
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array;

    /**
     * @param array<int|string, mixed>|null $criteria
     * @param array<string, array<int, string>>|null $search
     * @param array{skills: array<int, string>, languages: array<int, string>}|null $postFilters
     *
     * @return array{facets: array<int, array{key: string, sort: string, values: array<int, array{id: string, label: string, count: int}>}>}
     */
    public function computeFacets(
        ?array $criteria = null,
        ?array $search = null,
        ?array $postFilters = null,
        ?string $entityManagerName = null,
    ): array;
}
