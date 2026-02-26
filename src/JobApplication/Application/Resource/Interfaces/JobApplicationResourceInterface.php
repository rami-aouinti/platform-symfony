<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\JobApplication\Application\DTO\JobApplication\OfferApplicationPayload;
use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Enum\JobApplicationStatus;

/**
 * @package App\JobApplication
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
    public function findAllowedForCurrentUser(): array;

    /**
     * @return JobApplication[]
     */
    public function findForMyOffers(): array;

    public function getAllowedForCurrentUser(string $applicationId): JobApplication;
}
