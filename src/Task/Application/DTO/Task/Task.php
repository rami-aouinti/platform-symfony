<?php

declare(strict_types=1);

namespace App\Task\Application\DTO\Task;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task as Entity;
use App\Task\Domain\Enum\TaskPriority;
use App\Task\Domain\Enum\TaskStatus;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Task extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $title = '';

    protected ?string $description = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskPriority::class, 'getValues'])]
    protected string $priority = TaskPriority::MEDIUM->value;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskStatus::class, 'getValues'])]
    protected string $status = TaskStatus::TODO->value;

    #[AppAssert\EntityReferenceExists(Project::class)]
    protected ?Project $project = null;

    protected ?DateTimeImmutable $dueDate = null;

    protected ?DateTimeImmutable $completedAt = null;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->setVisited('title');
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->setVisited('description');
        $this->description = $description;

        return $this;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->setVisited('priority');
        $this->priority = $priority;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->setVisited('status');
        $this->status = $status;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->setVisited('project');
        $this->project = $project;

        return $this;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeImmutable $dueDate): self
    {
        $this->setVisited('dueDate');
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeImmutable $completedAt): self
    {
        $this->setVisited('completedAt');
        $this->completedAt = $completedAt;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->title = $entity->getTitle();
            $this->description = $entity->getDescription();
            $this->priority = $entity->getPriority()->value;
            $this->status = $entity->getStatus()->value;
            $this->project = $entity->getProject();
            $this->dueDate = $entity->getDueDate();
            $this->completedAt = $entity->getCompletedAt();
        }

        return $this;
    }
}
