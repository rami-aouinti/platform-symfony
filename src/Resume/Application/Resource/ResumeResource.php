<?php

declare(strict_types=1);

namespace App\Resume\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Resume\Application\Resource\Interfaces\ResumeResourceInterface;
use App\Resume\Domain\Entity\Resume as Entity;
use App\Resume\Domain\Repository\Interfaces\ResumeRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\Permission;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ResumeResource extends RestResource implements ResumeResourceInterface
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
        if ($entity instanceof Entity) {
            $entity->setOwner($this->getCurrentUser());
            $this->assertGranted(Permission::RESUME_CREATE->value, $entity, 'Only authenticated users can create a resume.');
        }
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::RESUME_EDIT->value, $entity, 'Only resume owner can edit this resume.');
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::RESUME_EDIT->value, $entity, 'Only resume owner can edit this resume.');
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::RESUME_DELETE->value, $entity, 'Only resume owner can delete this resume.');
        }
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertGranted(Permission::RESUME_VIEW->value, $entity, 'Resume not found.');
        }
    }

    public function findMyResumes(
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $search = null,
        ?string $entityManagerName = null,
    ): array {
        return $this->find(
            [
                'owner' => $this->getCurrentUser(),
            ],
            $orderBy,
            $limit,
            $offset,
            $search,
            $entityManagerName,
        );
    }

    private function assertGranted(string $permission, Entity $resume, string $message): void
    {
        if (!$this->authorizationChecker->isGranted($permission, $resume)) {
            throw new AccessDeniedHttpException($message);
        }
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
