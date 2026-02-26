<?php

declare(strict_types=1);

namespace App\Task\Application\DTO\TaskRequest;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Domain\Entity\Sprint;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest as Entity;
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

    #[AppAssert\EntityReferenceExists(Sprint::class)]
    protected ?Sprint $sprint = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskRequestType::class, 'getValues'])]
    protected string $type = TaskRequestType::STATUS_CHANGE->value;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [TaskStatus::class, 'getValues'])]
    protected ?string $requestedStatus = null;

    protected ?DateTimeImmutable $time = null;

    protected ?string $note = null;

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

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->setVisited('sprint');
        $this->sprint = $sprint;

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

    public function getTime(): ?DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(?DateTimeImmutable $time): self
    {
        $this->setVisited('time');
        $this->time = $time;

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

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->task = $entity->getTask();
            $this->sprint = $entity->getSprint();
            $this->type = $entity->getType()->value;
            $this->requestedStatus = $entity->getRequestedStatus()?->value;
            $this->time = $entity->getTime();
            $this->note = $entity->getNote();
        }

        return $this;
    }
}
