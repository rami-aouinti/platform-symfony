<?php

declare(strict_types=1);

namespace App\JobOffer\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface JobOfferResourceInterface extends RestResourceInterface
{
    /**
     * @return array<int, \App\JobOffer\Domain\Entity\JobOffer>
     */
    public function findMyOffers(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array;

    /**
     * @return array<int, \App\JobOffer\Domain\Entity\JobOffer>
     */
    public function findAvailableOffers(
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

