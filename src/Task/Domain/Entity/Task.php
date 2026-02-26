<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Task\Domain\Enum\TaskPriority;
use App\Task\Domain\Enum\TaskStatus;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'task')]
#[ORM\Index(name: 'idx_task_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_task_status', columns: ['status'])]
#[ORM\Index(name: 'idx_task_priority', columns: ['priority'])]
#[ORM\Index(name: 'idx_task_due_date', columns: ['due_date'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Task implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Task', 'Task.id', 'Task.show', 'Task.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Task', 'Task.owner', 'Task.show', 'Task.edit'])]
    private ?User $owner = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Task', 'Task.title', 'Task.create', 'Task.show', 'Task.edit'])]
    private string $title = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['Task', 'Task.description', 'Task.create', 'Task.show', 'Task.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'priority', type: Types::STRING, length: 64, nullable: false, enumType: TaskPriority::class)]
    #[Groups(['Task', 'Task.priority', 'Task.create', 'Task.show', 'Task.edit'])]
    private TaskPriority $priority = TaskPriority::MEDIUM;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: TaskStatus::class)]
    #[Groups(['Task', 'Task.status', 'Task.create', 'Task.show', 'Task.edit'])]
    private TaskStatus $status = TaskStatus::TODO;

    #[ORM\Column(name: 'due_date', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['Task', 'Task.dueDate', 'Task.create', 'Task.show', 'Task.edit'])]
    private ?DateTimeImmutable $dueDate = null;

    #[ORM\Column(name: 'completed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['Task', 'Task.completedAt', 'Task.create', 'Task.show', 'Task.edit'])]
    private ?DateTimeImmutable $completedAt = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPriority(): TaskPriority
    {
        return $this->priority;
    }

    public function setPriority(TaskPriority|string $priority): self
    {
        $this->priority = $priority instanceof TaskPriority ? $priority : TaskPriority::from($priority);

        return $this;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setStatus(TaskStatus|string $status): self
    {
        $nextStatus = $status instanceof TaskStatus ? $status : TaskStatus::from($status);

        if (!$this->status->canTransitionTo($nextStatus) && $this->status !== $nextStatus) {
            return $this;
        }

        $this->status = $nextStatus;

        if ($nextStatus === TaskStatus::DONE && $this->completedAt === null) {
            $this->completedAt = new DateTimeImmutable();
        }

        if ($nextStatus !== TaskStatus::DONE) {
            $this->completedAt = null;
        }

        return $this;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeImmutable $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }
}
