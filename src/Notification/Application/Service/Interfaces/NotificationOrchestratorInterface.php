<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Interfaces;

use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\User\Domain\Entity\User;

/**
 * @package App\Notification
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface NotificationOrchestratorInterface
{
    public function notifyCompanyCreated(User $owner, string $companyLegalName): void;

    public function notifyJobApplicationSubmitted(
        User $candidate,
        ?User $offerOwnerOrCreator,
        string $applicationId,
        string $offerId,
    ): void;

    public function notifyJobApplicationDecided(
        User $candidate,
        JobApplicationStatus $status,
        string $applicationId,
        string $offerId,
    ): void;
}

