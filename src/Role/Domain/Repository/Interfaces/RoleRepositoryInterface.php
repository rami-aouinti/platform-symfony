<?php

declare(strict_types=1);

namespace App\Role\Domain\Repository\Interfaces;

/**
 * @package App\Role\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface RoleRepositoryInterface
{
    /**
     * Method to clean existing roles from database that does not really exists.
     *
     * @param array<int, string> $roles
     */
    public function clearRoles(array $roles): int;
}
