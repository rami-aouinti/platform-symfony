<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Repository\Interfaces;

use App\JobOffer\Domain\Entity\JobOffer;
use App\User\Domain\Entity\User;

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
}
