<?php

declare(strict_types=1);

namespace App\Task\Application\DTO\TaskRequest;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest as Entity;
use App\Task\Domain\Enum\TaskRequestStatus;
use App\Task\Domain\Enum\TaskRequestType;
use App\Task\Domain\Enum\TaskStatus;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class TaskRequest extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Task::class)]
    protected ?Task $task = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskRequestType::class, 'getValues'])]
    protected string $type = TaskRequestType::STATUS_CHANGE->value;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskStatus::class, 'getValues'])]
    protected ?string $requestedStatus = null;

    protected ?string $note = null;

    #[Assert\Choice(callback: [TaskRequestStatus::class, 'getValues'])]
    protected string $status = TaskRequestStatus::PENDING->value;

    protected ?DateTimeImmutable $resolvedAt = null;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->setVisited('type');
        $this->type = $type;

        return $this;
    }

    public function getRequestedStatus(): ?string
    {
        return $this->requestedStatus;
    }

    public function setRequestedStatus(?string $requestedStatus): self
    {
        $this->setVisited('requestedStatus');
        $this->requestedStatus = $requestedStatus;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->setVisited('note');
        $this->note = $note;

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

    public function getResolvedAt(): ?DateTimeImmutable
    {
        return $this->resolvedAt;
    }

    public function setResolvedAt(?DateTimeImmutable $resolvedAt): self
    {
        $this->setVisited('resolvedAt');
        $this->resolvedAt = $resolvedAt;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->task = $entity->getTask();
            $this->type = $entity->getType()->value;
            $this->requestedStatus = $entity->getRequestedStatus()?->value;
            $this->note = $entity->getNote();
            $this->status = $entity->getStatus()->value;
            $this->resolvedAt = $entity->getResolvedAt();
        }

        return $this;
    }
}
