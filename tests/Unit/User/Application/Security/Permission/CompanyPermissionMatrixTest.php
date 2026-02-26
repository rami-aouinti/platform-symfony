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
        self::assertTrue($matrix->isGranted($user, Permission::JOB_APPLICATION_DECIDE, null, false));
    }

    public function testCrmManagerMembershipGrantsOfferAndApplicationPermissionsInCompanyContext(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([], 'company-1', 'crm_manager');

        self::assertTrue($matrix->isGranted($user, Permission::JOB_OFFER_MANAGE, 'company-1', false));
        self::assertTrue($matrix->isGranted($user, Permission::JOB_APPLICATION_DECIDE, 'company-1', false));
        self::assertFalse($matrix->isGranted($user, Permission::JOB_APPLICATION_WITHDRAW, 'company-1', false));
    }

    public function testCandidateCanWithdrawApplicationButCannotDecide(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([], 'company-1', 'candidate');

        self::assertTrue($matrix->isGranted($user, Permission::JOB_APPLICATION_WITHDRAW, 'company-1', false));
        self::assertFalse($matrix->isGranted($user, Permission::JOB_APPLICATION_DECIDE, 'company-1', false));
    }

    public function testCandidateGetsResumePermissionsFromMembership(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([], 'company-1', 'candidate');

        self::assertTrue($matrix->isGranted($user, Permission::RESUME_CREATE, 'company-1', false));
        self::assertTrue($matrix->isGranted($user, Permission::RESUME_EDIT, 'company-1', false));
        self::assertTrue($matrix->isGranted($user, Permission::RESUME_USE_FOR_APPLICATION, 'company-1', false));
    }

    public function testOwnershipFallbackWhenNoRoleMatches(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([]);

        self::assertTrue($matrix->isGranted($user, Permission::JOB_OFFER_MANAGE, null, true));
        self::assertTrue($matrix->isGranted($user, Permission::JOB_APPLICATION_DECIDE, null, true));
        self::assertFalse($matrix->isGranted($user, Permission::JOB_APPLICATION_WITHDRAW, null, true));
    }

    public function testOwnershipFallbackIncludesResumePermissions(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([]);

        self::assertTrue($matrix->isGranted($user, Permission::RESUME_DELETE, null, true));
        self::assertFalse($matrix->isGranted($user, Permission::RESUME_DELETE, null, false));
    }

    public function testNotificationPermissionGrantedFromMembershipWithoutExplicitCompanyContext(): void
    {
        $matrix = new CompanyPermissionMatrix();
        $user = $this->createSecurityUser([], 'company-1', 'owner');

        self::assertTrue($matrix->isGranted($user, Permission::NOTIFICATION_VIEW));
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
