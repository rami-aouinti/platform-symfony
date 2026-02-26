<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogCommentResourceInterface;
use App\Blog\Domain\Entity\BlogComment as Entity;
use App\Blog\Domain\Repository\Interfaces\BlogCommentRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BlogCommentResource extends RestResource implements BlogCommentResourceInterface
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
            $entity->setAuthor($this->getCurrentUser());
        }
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertAuthor($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertAuthor($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertAuthor($entity);
        }
    }

    private function assertAuthor(Entity $entity): void
    {
        if ($entity->getAuthor()?->getId() === $this->getCurrentUser()->getId()) {
            return;
        }

        throw new AccessDeniedHttpException('Only comment author can manage this comment.');
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
