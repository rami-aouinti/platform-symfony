<?php

declare(strict_types=1);

namespace App\Blog\Application\DTO\BlogPostLink;

use App\Blog\Domain\Entity\BlogPost;
use App\Blog\Domain\Entity\BlogPostLink as Entity;
use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class BlogPostLink extends RestDto
{
    #[AppAssert\EntityReferenceExists(BlogPost::class)]
    protected ?BlogPost $post = null;

    #[AppAssert\EntityReferenceExists(Task::class)]
    protected ?Task $task = null;

    #[AppAssert\EntityReferenceExists(TaskRequest::class)]
    protected ?TaskRequest $taskRequest = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [BlogReferenceType::class, 'getValues'])]
    protected string $referenceType = BlogReferenceType::TASK->value;

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->setVisited('post');
        $this->post = $post;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->setVisited('task');
        $this->task = $task;

        return $this;
    }

    public function getTaskRequest(): ?TaskRequest
    {
        return $this->taskRequest;
    }

    public function setTaskRequest(?TaskRequest $taskRequest): self
    {
        $this->setVisited('taskRequest');
        $this->taskRequest = $taskRequest;

        return $this;
    }

    public function getReferenceType(): string
    {
        return $this->referenceType;
    }

    public function setReferenceType(string $referenceType): self
    {
        $this->setVisited('referenceType');
        $this->referenceType = $referenceType;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->post = $entity->getPost();
            $this->task = $entity->getTask();
            $this->taskRequest = $entity->getTaskRequest();
            $this->referenceType = $entity->getReferenceType()->value;
        }

        return $this;
    }
}
