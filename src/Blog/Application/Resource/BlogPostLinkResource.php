<?php

declare(strict_types=1);

namespace App\Blog\Application\Resource;

use App\Blog\Application\Resource\Interfaces\BlogPostLinkResourceInterface;
use App\Blog\Domain\Entity\BlogPostLink as Entity;
use App\Blog\Domain\Enum\BlogReferenceType;
use App\Blog\Domain\Repository\Interfaces\BlogPostLinkRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BlogPostLinkResource extends RestResource implements BlogPostLinkResourceInterface
{
    public function __construct(RepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertValidTarget($entity);
        }
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertValidTarget($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertValidTarget($entity);
        }
    }

    private function assertValidTarget(Entity $entity): void
    {
        $hasTask = $entity->getTask() !== null;
        $hasTaskRequest = $entity->getTaskRequest() !== null;

        if ($hasTask === $hasTaskRequest) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'A blog link must target exactly one of task or taskRequest.');
        }

        if ($entity->getReferenceType() === BlogReferenceType::TASK && !$hasTask) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'referenceType task requires task target.');
        }

        if ($entity->getReferenceType() === BlogReferenceType::TASK_REQUEST && !$hasTaskRequest) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'referenceType task_request requires taskRequest target.');
        }
    }
}
