<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogPostResourceInterface;
use App\Blog\Domain\Entity\BlogPost as Entity;
use App\Blog\Domain\Repository\Interfaces\BlogPostRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\AbstractOwnedResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\User\Application\Security\UserTypeIdentification;

class BlogPostResource extends AbstractOwnedResource implements BlogPostResourceInterface
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
            $entity->setOwner($this->getCurrentUserOrDeny());
        }
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertOwner($entity);
        }
    }

    private function assertOwner(Entity $entity): void
    {
        $this->assertOwnerOrDeny(
            $entity->getOwner()?->getId() === $this->getCurrentUserOrDeny()->getId(),
            'Only post owner can manage this post.',
        );
    }
}
