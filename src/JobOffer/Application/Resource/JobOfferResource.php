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
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class JobOfferResource extends RestResource implements JobOfferResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
    ) {
        parent::__construct($repository);
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

    private function assertGranted(string $permission, Entity $offer): void
    {
        if (!$this->authorizationChecker->isGranted($permission, $offer)) {
            throw new AccessDeniedHttpException('Only offer author, company owner or manager can manage offers.');
        }
    }
}
