<?php

declare(strict_types=1);

namespace App\JobApplication\Application\Resource\Interfaces;

use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Enum\ApplicationStatus;

interface JobApplicationResourceInterface
{
    public function apply(string $offerId): JobApplication;

    public function withdraw(string $applicationId): JobApplication;

    public function decide(string $applicationId, ApplicationStatus $status): JobApplication;

    /**
     * @return JobApplication[]
     */
    public function findAllowedForCurrentUser(): array;

    public function getAllowedForCurrentUser(string $applicationId): JobApplication;
}
