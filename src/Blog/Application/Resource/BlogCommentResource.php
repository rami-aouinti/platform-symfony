<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogCommentResourceInterface;
use App\Blog\Domain\Entity\BlogComment as Entity;
use App\Blog\Domain\Repository\Interfaces\BlogCommentRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\AbstractOwnedResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Application\Security\UserTypeIdentification;

class BlogCommentResource extends AbstractOwnedResource implements BlogCommentResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        UserTypeIdentification $userTypeIdentification,
    ) {
        parent::__construct($repository, $userTypeIdentification);
    }

    protected function onBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $entity->setAuthor($this->getCurrentUserOrDeny());
        }
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertAuthor($entity);
        }
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertAuthor($entity);
        }
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertAuthor($entity);
        }
    }

    private function assertAuthor(Entity $entity): void
    {
        $this->assertOwnerOrDeny(
            $entity->getAuthor()?->getId() === $this->getCurrentUserOrDeny()->getId(),
            'Only comment author can manage this comment.',
        );
    }
}
