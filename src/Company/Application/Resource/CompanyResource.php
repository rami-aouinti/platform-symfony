<?php

declare(strict_types=1);

namespace App\Company\Application\Resource;

use App\Company\Application\Resource\Interfaces\CompanyResourceInterface;
use App\Company\Domain\Entity\Company as Entity;
use App\Company\Domain\Entity\CompanyMembership;
use App\Company\Domain\Repository\Interfaces\CompanyMembershipRepositoryInterface;
use App\Company\Domain\Repository\Interfaces\CompanyRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Company\Domain\Message\CompanyCreatedMessage;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use function in_array;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @method Entity create(RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity update(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity patch(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity delete(string $id, ?bool $flush = null, ?string $entityManagerName = null)
 * @package App\Company
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class CompanyResource extends RestResource implements CompanyResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly CompanyMembershipRepositoryInterface $companyMembershipRepository,
        private readonly MessageServiceInterface $messageService,
    ) {
        parent::__construct($repository);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        if ($this->isAdminLike($this->getCurrentUser())) {
            return;
        }

        $criteria['owner'] = $this->getCurrentUser();
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    public function afterCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity) {
            return;
        }

        $currentUser = $this->userTypeIdentification->getUser();

        if ($currentUser === null) {
            throw new RuntimeException('Cannot create company membership without authenticated user.');
        }

        $entity->setOwner($currentUser);
        $entity->addMembership(
            (new CompanyMembership($currentUser, $entity))
                ->setRole(CompanyMembership::ROLE_OWNER)
                ->setStatus('active')
                ->setJoinedAt(new DateTimeImmutable())
        );

        $this->messageService->sendMessage(new CompanyCreatedMessage(
            companyId: $entity->getId(),
            ownerUserId: $currentUser->getId(),
            metadata: [
                'legalName' => $entity->getLegalName(),
                'slug' => $entity->getSlug(),
            ],
        ));
    }

    private function assertOwner(Entity $company): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->isAdminLike($currentUser) || $company->getOwner()?->getId() === $currentUser->getId()) {
            return;
        }

        $membership = $this->companyMembershipRepository->findOneBy([
            'user' => $currentUser,
            'company' => $company,
        ]);

        if ($membership instanceof CompanyMembership && $membership->getRole() === CompanyMembership::ROLE_OWNER) {
            return;
        }

        throw new AccessDeniedHttpException('You are not allowed to access this company.');
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
