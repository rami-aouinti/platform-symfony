<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'sprint')]
#[ORM\Index(name: 'idx_sprint_start_date', columns: ['start_date'])]
#[ORM\Index(name: 'idx_sprint_end_date', columns: ['end_date'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Sprint implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Sprint', 'Sprint.id', 'Sprint.show', 'Sprint.edit'])]
    private UuidInterface $id;

    /** @var Collection<int, TaskRequest> */
    #[ORM\OneToMany(mappedBy: 'sprint', targetEntity: TaskRequest::class)]
    #[Groups(['Sprint', 'Sprint.taskRequests', 'Sprint.show'])]
    private Collection $taskRequests;

    #[ORM\Column(name: 'start_date', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Groups(['Sprint', 'Sprint.startDate', 'Sprint.create', 'Sprint.show', 'Sprint.edit'])]
    private ?DateTimeImmutable $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Groups(['Sprint', 'Sprint.endDate', 'Sprint.create', 'Sprint.show', 'Sprint.edit'])]
    private ?DateTimeImmutable $endDate = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->taskRequests = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    /** @return Collection<int, TaskRequest> */
    public function getTaskRequests(): Collection
    {
        return $this->taskRequests;
    }

    public function addTaskRequest(TaskRequest $taskRequest): self
    {
        if (!$this->taskRequests->contains($taskRequest)) {
            $this->taskRequests->add($taskRequest);
            $taskRequest->setSprint($this);
        }

        return $this;
    }

    public function removeTaskRequest(TaskRequest $taskRequest): self
    {
        if ($this->taskRequests->removeElement($taskRequest) && $taskRequest->getSprint() === $this) {
            $taskRequest->setSprint(null);
        }

        return $this;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}
