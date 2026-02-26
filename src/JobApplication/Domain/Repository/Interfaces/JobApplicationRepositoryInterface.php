<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Repository\Interfaces;

use App\JobApplication\Domain\Entity\JobApplication;
use App\User\Domain\Entity\User;

interface JobApplicationRepositoryInterface
{
    /**
     * @return JobApplication[]
     */
    public function findForMyOffers(User $user): array;
}
