<?php

declare(strict_types=1);

namespace App\Company\Application\Resource;

use App\Company\Application\Resource\Interfaces\CompanyMembershipResourceInterface;
use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyMembership as Entity;
use App\Company\Domain\Enum\CompanyMembershipStatus;
use App\Company\Domain\Repository\Interfaces\CompanyMembershipRepositoryInterface as RepositoryInterface;
use App\Company\Domain\Repository\Interfaces\CompanyRepositoryInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindMethod;
use App\General\Application\Rest\Traits\Methods\ResourceSaveMethod;
use App\Role\Domain\Enum\Role;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function in_array;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Company\Application\Resource
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class CompanyMembershipResource extends RestSmallResource implements CompanyMembershipResourceInterface
{
    use ResourceFindMethod;
    use ResourceSaveMethod;

    public function __construct(
        RepositoryInterface $repository,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
        parent::__construct($repository);
    }

    public function inviteOrAttach(
        string $companyId,
        string $userId,
        ?string $role = null,
        ?string $status = null,
    ): Entity {
        $company = $this->getCompany($companyId);
        $this->assertCanManageCompany($company);

        $user = $this->getUser($userId);
        $membership = $this->getRepository()->findOneBy([
            'company' => $company,
            'user' => $user,
        ]);

        if (!$membership instanceof Entity) {
            $membership = new Entity($user, $company);
        }

        $membership
            ->setRole($role ?? Entity::ROLE_MEMBER)
            ->setStatus($status === null ? CompanyMembershipStatus::INVITED : CompanyMembershipStatus::from($status));

        if ($membership->getStatus() === CompanyMembershipStatus::INVITED) {
            $membership->setInvitedAt(new DateTimeImmutable());
        }

        if ($membership->getStatus() === CompanyMembershipStatus::ACTIVE) {
            $membership->setJoinedAt($membership->getJoinedAt() ?? new DateTimeImmutable());
        }

        return $this->save($membership);
    }

    public function updateMembership(
        string $companyId,
        string $userId,
        ?string $role = null,
        ?string $status = null,
    ): Entity {
        $company = $this->getCompany($companyId);
        $this->assertCanManageCompany($company);

        $membership = $this->findMembership($company, $this->getUser($userId));

        if (is_string($role) && $role !== '') {
            $membership->setRole($role);
        }

        if (is_string($status) && $status !== '') {
            $membership->setStatus(CompanyMembershipStatus::from($status));

            if ($membership->getStatus() === CompanyMembershipStatus::ACTIVE) {
                $membership->setJoinedAt($membership->getJoinedAt() ?? new DateTimeImmutable());
            }
        }

        return $this->save($membership);
    }

    public function removeMembership(string $companyId, string $userId): void
    {
        $company = $this->getCompany($companyId);
        $this->assertCanManageCompany($company);

        $membership = $this->findMembership($company, $this->getUser($userId));

        $this->getRepository()->remove($membership, true);
    }

    public function findByCompany(string $companyId): array
    {
        $company = $this->getCompany($companyId);
        $this->assertCanReadCompany($company);

        return $this->find(criteria: ['company' => $company]);
    }

    public function findMyCompanies(): array
    {
        $currentUser = $this->getCurrentUser();

        return $this->find(criteria: ['user' => $currentUser]);
    }

    public function findMyMembership(string $companyId): ?Entity
    {
        $company = $this->getCompany($companyId);
        $this->assertCanReadCompany($company);

        $currentUser = $this->getCurrentUser();

        $membership = $this->getRepository()->findOneBy([
            'company' => $company,
            'user' => $currentUser,
        ]);

        return $membership instanceof Entity ? $membership : null;
    }

    private function getCompany(string $companyId): Company
    {
        $company = $this->companyRepository->find($companyId);

        if (!$company instanceof Company) {
            throw new NotFoundHttpException('Company not found.');
        }

        return $company;
    }

    private function getUser(string $userId): User
    {
        $user = $this->userRepository->find($userId);

        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        return $user;
    }

    private function findMembership(Company $company, User $user): Entity
    {
        $membership = $this->getRepository()->findOneBy([
            'company' => $company,
            'user' => $user,
        ]);

        if (!$membership instanceof Entity) {
            throw new NotFoundHttpException('Membership not found.');
        }

        return $membership;
    }

    private function assertCanManageCompany(Company $company): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser) || $company->getOwner()?->getId() === $currentUser->getId()) {
            return;
        }

        $membership = $this->getRepository()->findOneBy([
            'company' => $company,
            'user' => $currentUser,
        ]);

        if ($membership instanceof Entity && $membership->getRole() === Entity::ROLE_OWNER) {
            return;
        }

        throw new AccessDeniedHttpException('You are not allowed to manage memberships for this company.');
    }

    private function assertCanReadCompany(Company $company): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser) || $company->getOwner()?->getId() === $currentUser->getId()) {
            return;
        }

        $membership = $this->getRepository()->findOneBy([
            'company' => $company,
            'user' => $currentUser,
        ]);

        if ($membership instanceof Entity) {
            return;
        }

        throw new AccessDeniedHttpException('You are not allowed to access memberships for this company.');
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }

    private function isAdminLike(User $user): bool
    {
        return in_array(Role::ROOT->value, $user->getRoles(), true)
            || in_array(Role::ADMIN->value, $user->getRoles(), true);
    }
}
