<?php

declare(strict_types=1);

namespace App\User\Domain\Repository\Interfaces;

use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserProfile as Entity;

/**
 * @package App\User
 */
interface UserProfileRepositoryInterface
{
    public function findOneByUser(User $user): ?Entity;
}
