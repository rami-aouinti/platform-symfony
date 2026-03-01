<?php

declare(strict_types=1);

namespace App\Company\Application\Resource\Interfaces;

use App\Company\Domain\Entity\CompanyMembership;
use App\General\Application\Rest\Interfaces\RestSmallResourceInterface;

/**
 * @package App\Company\Application\Resource\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface CompanyMembershipResourceInterface extends RestSmallResourceInterface
{
    public function inviteOrAttach(
        string $companyId,
        string $userId,
        ?string $role = null,
        ?string $status = null,
    ): CompanyMembership;

    public function updateMembership(
        string $companyId,
        string $userId,
        ?string $role = null,
        ?string $status = null,
    ): CompanyMembership;

    public function removeMembership(string $companyId, string $userId): void;

    /**
     * @return array<int, CompanyMembership>
     */
    public function findByCompany(string $companyId): array;

    /**
     * @return array<int, CompanyMembership>
     */
    public function findMyCompanies(): array;

    public function findMyMembership(string $companyId): ?CompanyMembership;
}
