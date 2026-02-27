<?php

declare(strict_types=1);

namespace App\Log\Domain\Repository\Interfaces;

use App\User\Domain\Entity\User;

/**
 * @package App\Log\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface LogLoginFailureRepositoryInterface
{
    /**
     * Method to clear specified user login failures.
     */
    public function clear(User $user): int;
}
