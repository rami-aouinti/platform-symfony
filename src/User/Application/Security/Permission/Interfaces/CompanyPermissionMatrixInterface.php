<?php

declare(strict_types=1);

namespace App\User\Application\Security\Permission\Interfaces;

use App\User\Application\Security\Permission;
use App\User\Application\Security\SecurityUser;

interface CompanyPermissionMatrixInterface
{
    public function isGranted(
        SecurityUser $user,
        Permission|string $permission,
        ?string $companyId = null,
        bool $isOwner = false,
    ): bool;
}
