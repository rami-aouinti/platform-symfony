<?php

declare(strict_types=1);

namespace App\JobOffer\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\JobOffer\Application\DTO\JobOffer\JobOffer as JobOfferDto;
use App\JobOffer\Application\Resource\Interfaces\JobOfferResourceInterface;
use App\JobOffer\Domain\Entity\JobOffer as Entity;
use App\JobOffer\Domain\Repository\Interfaces\JobOfferRepositoryInterface as RepositoryInterface;
use App\Role\Domain\Enum\Role;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use function in_array;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class JobOfferResource extends RestResource implements JobOfferResourceInterface
{
    private readonly RepositoryInterface $jobOfferRepository;

    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($repository);
        $this->jobOfferRepository = $repository;
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity || !$restDto instanceof JobOfferDto) {
            return;
        }

        $user = $this->userTypeIdentification->getUser();

        if ($user instanceof User) {
            $entity->setCreatedBy($user);
        }

        $this->assertGranted(Permission::JOB_OFFER_MANAGE->value, $entity);
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::JOB_OFFER_MANAGE->value, $entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::JOB_OFFER_MANAGE->value, $entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::JOB_OFFER_MANAGE->value, $entity);
        }
    }

    public function findMyOffers(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array {
        $user = $this->getCurrentUser();
        $hasGlobalManagePermission = $this->hasGlobalPermission($user);

        return $this->jobOfferRepository->findMyOffersQuery(
            $user,
            $hasGlobalManagePermission,
            $criteria,
            $orderBy,
            $limit,
            $offset,
            $search,
            $entityManagerName,
        );
    }

    public function findAvailableOffers(
        ?array $criteria = null,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array {
        $user = $this->getCurrentUser();
        $hasGlobalApplyPermission = $this->hasGlobalPermission($user);

        return $this->jobOfferRepository->findAvailableOffersQuery(
            $user,
            $hasGlobalApplyPermission,
            $criteria,
            $orderBy,
            $limit,
            $offset,
            $search,
            $entityManagerName,
        );
    }


    public function computeFacets(
        ?array $criteria = null,
        ?array $search = null,
        ?array $postFilters = null,
        ?string $entityManagerName = null,
    ): array {
        return $this->jobOfferRepository->computeFacets(
            $criteria,
            $search,
            $postFilters,
            $entityManagerName,
        );
    }

    private function hasGlobalPermission(User $user): bool
    {
        return in_array(Role::ROOT->value, $user->getRoles(), true)
            || in_array(Role::ADMIN->value, $user->getRoles(), true);
    }

    private function assertGranted(string $permission, Entity $offer): void
    {
        if (!$this->authorizationChecker->isGranted($permission, $offer)) {
            throw new AccessDeniedHttpException('Only offer author, company owner or manager can manage offers.');
        }
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'Authenticated user not found.');
        }

        return $user;
    }
}
