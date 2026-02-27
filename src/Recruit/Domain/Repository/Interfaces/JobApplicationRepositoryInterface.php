<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Repository\Interfaces;

use App\Recruit\Domain\Entity\JobApplication;
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

    /**
     * @return JobApplication[]
     */
    public function findAllowedForUser(
        User $user,
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array;
}
