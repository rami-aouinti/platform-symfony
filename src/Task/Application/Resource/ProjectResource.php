<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyMembership;
use App\Company\Domain\Enum\CompanyMembershipStatus;
use App\Company\Domain\Repository\Interfaces\CompanyMembershipRepositoryInterface;
use App\Company\Domain\Repository\Interfaces\CompanyRepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\AbstractOwnedResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\Resource\Interfaces\ProjectResourceInterface;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Project as Entity;
use App\Task\Domain\Repository\Interfaces\ProjectRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Task\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class ProjectResource extends AbstractOwnedResource implements ProjectResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        UserTypeIdentification $userTypeIdentification,
        private readonly TaskAccessServiceInterface $taskAccessService,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly CompanyMembershipRepositoryInterface $companyMembershipRepository,
    ) {
        parent::__construct($repository, $userTypeIdentification);
    }


    /**
     * @return array<int, Entity>
     */
    public function findMyAccessibleProjects(User $currentUser): array
    {
        $projects = [];

        foreach ($this->getRepository()->findByAdvanced(criteria: ['owner' => $currentUser]) as $project) {
            if ($project instanceof Entity) {
                $projects[$project->getId()] = $project;
            }
        }

        $companies = [];

        foreach ($this->companyRepository->findBy(['owner' => $currentUser]) as $company) {
            if ($company instanceof Company) {
                $companies[$company->getId()] = $company;
            }
        }

        foreach ($this->companyMembershipRepository->findBy([
            'user' => $currentUser,
            'status' => CompanyMembershipStatus::ACTIVE,
        ]) as $membership) {
            if (!$membership instanceof CompanyMembership) {
                continue;
            }

            $company = $membership->getCompany();

            if ($company !== null) {
                $companies[$company->getId()] = $company;
            }
        }

        if ($companies !== []) {
            foreach ($this->getRepository()->findByAdvanced(criteria: ['company' => array_values($companies)]) as $project) {
                if ($project instanceof Entity) {
                    $projects[$project->getId()] = $project;
                }
            }
        }

        usort($projects, static fn (Entity $left, Entity $right): int => $left->getName() <=> $right->getName());

        return array_values($projects);
    }

    /**
     * @param string $companyId
     * @param User   $currentUser
     *
     * @throws Throwable
     * @return array<int, Entity>
     */
    public function findProjectsForMyCompanyAccess(string $companyId, User $currentUser): array
    {
        $company = $this->companyRepository->find($companyId);

        if ($company === null) {
            return [];
        }

        if (!$this->canAccessCompanyProjects($company, $currentUser)) {
            throw new AccessDeniedHttpException('You are not allowed to access projects for this company.');
        }

        return $this->find(criteria: [
            'company' => $company,
        ]);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $currentUser = $this->getCurrentUserOrDeny();

        if ($this->taskAccessService->isAdminLike($currentUser)) {
            return;
        }

        $criteria['owner'] = $currentUser;
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageProject($entity);
        }
    }

    protected function onBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $currentUser = $this->getCurrentUserOrDeny();
            $entity->setOwner($currentUser);

            $company = $this->companyRepository->findOneBy([
                'owner' => $currentUser,
            ]);

            if ($company === null) {
                throw new AccessDeniedHttpException('No company found for current user.');
            }

            $entity->setCompany($company);
        }
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageProject($entity);
        }
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageProject($entity);
        }
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageProject($entity);
        }
    }

    private function canAccessCompanyProjects(Company $company, User $currentUser): bool
    {
        if ($this->taskAccessService->isAdminLike($currentUser)) {
            return true;
        }

        if ($company?->getOwner()?->getId() === $currentUser->getId()) {
            return true;
        }

        $membership = $this->companyMembershipRepository->findOneBy([
            'company' => $company,
            'user' => $currentUser,
            'status' => CompanyMembershipStatus::ACTIVE,
        ]);

        return $membership instanceof CompanyMembership;
    }

    private function assertCanManageProject(Entity $project): void
    {
        $currentUser = $this->getCurrentUserOrDeny();

        $this->assertOwnerOrDeny(
            $this->taskAccessService->canManageProject($currentUser, $project),
            'Only project owner can manage this project.',
        );
    }
}
