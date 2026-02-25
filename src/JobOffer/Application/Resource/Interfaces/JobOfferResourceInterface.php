<?php

declare(strict_types=1);

namespace App\JobOffer\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;

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
}
