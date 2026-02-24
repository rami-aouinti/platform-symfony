<?php

declare(strict_types=1);

namespace App\Tests\Unit\User\Application\Security\Permission;

use App\User\Application\Security\Permission;
use App\User\Application\Security\Permission\CompanyPermissionMatrix;
use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class CompanyPermissionMatrixTest extends TestCase
{
    public function testGlobalRoleHasPriority(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser(['ROLE_ADMIN']);

        self::assertTrue($matrix->isGranted($user, Permission::SHOP_MANAGE, null, false));
    }

    public function testMembershipRoleCanGrantInCompanyContext(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([], 'company-1', 'shop_admin');

        self::assertTrue($matrix->isGranted($user, Permission::SHOP_MANAGE, 'company-1', false));
        self::assertFalse($matrix->isGranted($user, Permission::CRM_MANAGE, 'company-1', false));
    }

    public function testOwnershipFallbackWhenNoRoleMatches(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([]);

        self::assertTrue($matrix->isGranted($user, Permission::BLOG_VIEW, null, true));
        self::assertFalse($matrix->isGranted($user, Permission::BLOG_MANAGE, null, true));
    }

    public function testNotificationPermissionGrantedFromMembershipWithoutExplicitCompanyContext(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([], 'company-1', 'member');

        self::assertTrue($matrix->isGranted($user, Permission::NOTIFICATION_VIEW));
        self::assertTrue($matrix->isGranted($user, Permission::NOTIFICATION_MANAGE));
    }

    public function testNotificationPermissionDeniedWhenNoRoleMatches(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([]);

        self::assertFalse($matrix->isGranted($user, Permission::NOTIFICATION_VIEW));
    }

    private function createSecurityUser(array $roles, ?string $companyId = null, ?string $membershipRole = null): SecurityUser
    {
        return new class(new User(), $roles, $companyId, $membershipRole) extends SecurityUser {
            public function __construct(
                User $user,
                array $roles,
                private readonly ?string $companyId,
                private readonly ?string $membershipRole,
            ) {
                parent::__construct($user, $roles);
            }

            public function getMembershipRole(string $companyId): ?string
            {
                if ($this->companyId === $companyId) {
                    return $this->membershipRole;
                }

                return null;
            }

            public function getOrganizations(): array
            {
                if ($this->companyId === null || $this->membershipRole === null) {
                    return [];
                }

                return [[
                    'companyId' => $this->companyId,
                    'role' => $this->membershipRole,
                    'status' => 'active',
                ]];
            }
        };
    }
}
