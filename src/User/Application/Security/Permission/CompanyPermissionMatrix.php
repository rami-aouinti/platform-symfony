<?php

declare(strict_types=1);

namespace App\User\Application\Security\Permission;

use App\Company\Domain\Entity\CompanyMembership;
use App\Role\Domain\Enum\Role;
use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\Interfaces\CompanyPermissionMatrixInterface;
use App\User\Application\Security\SecurityUser;
use Override;

use function in_array;

readonly class CompanyPermissionMatrix implements CompanyPermissionMatrixInterface
{
    /**
     * Resolution order:
     *  1) global role
     *  2) company membership role
     *  3) ownership fallback
     */
    #[Override]
    public function isGranted(
        SecurityUser $user,
        Permission|string $permission,
        ?string $companyId = null,
        bool $isOwner = false,
    ): bool {
        $normalizedPermission = $permission instanceof Permission ? $permission : Permission::from($permission);

        if ($this->hasGlobalGrant($user)) {
            return true;
        }

        if ($companyId !== null) {
            $membershipRole = $user->getMembershipRole($companyId);

            if ($membershipRole !== null && $this->membershipGrantsPermission($membershipRole, $normalizedPermission)) {
                return true;
            }
        } else {
            foreach ($user->getOrganizations() as $organization) {
                if ($this->membershipGrantsPermission($organization['role'], $normalizedPermission)) {
                    return true;
                }
            }
        }

        return $isOwner && in_array($normalizedPermission, $this->ownershipPermissions(), true);
    }

    private function hasGlobalGrant(SecurityUser $user): bool
    {
        return in_array(Role::ROOT->value, $user->getRoles(), true)
            || in_array(Role::ADMIN->value, $user->getRoles(), true);
    }

    private function membershipGrantsPermission(string $membershipRole, Permission $permission): bool
    {
        return in_array($permission, $this->matrix()[$membershipRole] ?? [], true);
    }

    /**
     * @return array<string, list<Permission>>
     */
    private function matrix(): array
    {
        return [
            CompanyMembership::ROLE_OWNER => [
                Permission::BLOG_VIEW,
                Permission::CRM_VIEW,
                Permission::CRM_MANAGE,
                Permission::SHOP_VIEW,
                Permission::SHOP_MANAGE,
                Permission::EDUCATION_VIEW,
                Permission::EDUCATION_MANAGE,
                Permission::NOTIFICATION_VIEW,
            ],
            CompanyMembership::ROLE_MEMBER => [
                Permission::BLOG_VIEW,
                Permission::CRM_VIEW,
                Permission::SHOP_VIEW,
                Permission::EDUCATION_VIEW,
                Permission::NOTIFICATION_VIEW,
            ],
            CompanyMembership::ROLE_CRM_MANAGER => [
                Permission::CRM_VIEW,
                Permission::CRM_MANAGE,
                Permission::NOTIFICATION_VIEW,
            ],
            CompanyMembership::ROLE_SHOP_ADMIN => [
                Permission::SHOP_VIEW,
                Permission::SHOP_MANAGE,
                Permission::NOTIFICATION_VIEW,
            ],
            CompanyMembership::ROLE_TEACHER => [
                Permission::EDUCATION_VIEW,
                Permission::EDUCATION_MANAGE,
                Permission::NOTIFICATION_VIEW,
            ],
            CompanyMembership::ROLE_CANDIDATE => [
                Permission::EDUCATION_VIEW,
                Permission::NOTIFICATION_VIEW,
            ],
        ];
    }

    /**
     * @return list<Permission>
     */
    private function ownershipPermissions(): array
    {
        return [
            Permission::BLOG_VIEW,
            Permission::CRM_VIEW,
            Permission::CRM_MANAGE,
            Permission::SHOP_VIEW,
            Permission::SHOP_MANAGE,
            Permission::EDUCATION_VIEW,
            Permission::EDUCATION_MANAGE,
            Permission::NOTIFICATION_VIEW,
        ];
    }
}
