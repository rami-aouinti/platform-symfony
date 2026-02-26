<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Repository\Interfaces;

use App\JobApplication\Domain\Entity\JobApplication;
use App\User\Domain\Entity\User;

/**
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface JobApplicationRepositoryInterface
{
    /**
     * @return JobApplication[]
     */
    public function findForMyOffers(User $user): array;
}
