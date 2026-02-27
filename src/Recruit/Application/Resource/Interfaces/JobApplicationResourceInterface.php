<?php

declare(strict_types=1);

namespace App\Recruit\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Recruit\Application\DTO\JobApplication\OfferApplicationPayload;
use App\Recruit\Domain\Entity\JobApplication;
use App\Recruit\Domain\Enum\JobApplicationStatus;

/**
 * @package App\Recruit\Application\Resource\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface JobApplicationResourceInterface extends RestResourceInterface
{
    public function apply(string $jobOfferId, ?OfferApplicationPayload $payload = null): JobApplication;

    public function withdraw(string $applicationId): JobApplication;

    public function decide(string $applicationId, JobApplicationStatus $status): JobApplication;

    /**
     * @return JobApplication[]
     */
    public function findAllowedForCurrentUser(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array;

    /**
     * @return JobApplication[]
     */
    public function findForMyOffers(): array;

    public function getAllowedForCurrentUser(string $applicationId): JobApplication;
}
