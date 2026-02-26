<?php

declare(strict_types=1);

namespace App\Offer\Application\Resource;

use App\Company\Domain\Entity\CompanyMembership;
use App\Company\Infrastructure\Repository\CompanyMembershipRepository;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Offer\Application\DTO\Offer\Offer as OfferDto;
use App\Offer\Application\Resource\Interfaces\OfferResourceInterface;
use App\Offer\Domain\Entity\Offer as Entity;
use App\Offer\Domain\Repository\Interfaces\OfferRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use function array_map;
use function in_array;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Offer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class OfferResource extends RestResource implements OfferResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly CompanyMembershipRepository $companyMembershipRepository,
    ) {
        parent::__construct($repository);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser)) {
            return;
        }

        $membershipCompanyIds = array_map(
            static fn (CompanyMembership $membership): string => $membership->getCompany()->getId(),
            $this->companyMembershipRepository->findBy([
                'user' => $currentUser,
            ]),
        );

        $criteria = [
            'company' => $membershipCompanyIds,
        ];
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageOffer($entity);
        }
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity || !$restDto instanceof OfferDto) {
            return;
        }

        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser)) {
            $entity->setCreatedBy($currentUser);

            return;
        }

        $company = $restDto->getCompany();

        if ($company === null) {
            throw new AccessDeniedHttpException('Offer company is required.');
        }

        $membership = $this->companyMembershipRepository->findOneBy([
            'user' => $currentUser,
            'company' => $company,
        ]);

        if (
            !$membership instanceof CompanyMembership
            || !in_array($membership->getRole(), [CompanyMembership::ROLE_OWNER, CompanyMembership::ROLE_CRM_MANAGER], true)
        ) {
            throw new AccessDeniedHttpException('Only offer author, company owner or manager can manage offers.');
        }

        $entity->setCreatedBy($currentUser);
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageOffer($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageOffer($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageOffer($entity);
        }
    }

    private function assertCanManageOffer(Entity $offer): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser) || $offer->getCreatedBy()?->getId() === $currentUser->getId()) {
            return;
        }

        $membership = $this->companyMembershipRepository->findOneBy([
            'user' => $currentUser,
            'company' => $offer->getCompany(),
        ]);

        if (
            $membership instanceof CompanyMembership
            && in_array($membership->getRole(), [CompanyMembership::ROLE_OWNER, CompanyMembership::ROLE_CRM_MANAGER], true)
        ) {
            return;
        }

        throw new AccessDeniedHttpException('Only offer author, company owner or manager can manage offers.');
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
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
