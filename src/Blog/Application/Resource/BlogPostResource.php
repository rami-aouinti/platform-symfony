<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogPostResourceInterface;
use App\Blog\Domain\Entity\BlogPost as Entity;
use App\Blog\Domain\Repository\Interfaces\BlogPostRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BlogPostResource extends RestResource implements BlogPostResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
        parent::__construct($repository);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $entity->setOwner($this->getCurrentUser());
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

    private function assertOwner(Entity $entity): void
    {
        if ($entity->getOwner()?->getId() === $this->getCurrentUser()->getId()) {
            return;
        }

        throw new AccessDeniedHttpException('Only post owner can manage this post.');
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
