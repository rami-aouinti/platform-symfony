<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Task\Domain\Enum\TaskRequestType;
use App\Task\Domain\Enum\TaskStatus;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * TaskRequest.
 *
 * @package App\Task\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'task_request')]
#[ORM\Index(name: 'idx_task_request_task_id', columns: ['task_id'])]
#[ORM\Index(name: 'idx_task_request_requester_id', columns: ['requester_id'])]
#[ORM\Index(name: 'idx_task_request_reviewer_id', columns: ['reviewer_id'])]
#[ORM\Index(name: 'idx_task_request_sprint_id', columns: ['sprint_id'])]
#[ORM\Index(name: 'idx_task_request_type', columns: ['type'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class TaskRequest implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.id', 'TaskRequest.show', 'TaskRequest.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.task', 'TaskRequest.create', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?Task $task = null;

    #[ORM\ManyToOne(targetEntity: Sprint::class, inversedBy: 'taskRequests')]
    #[ORM\JoinColumn(name: 'sprint_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['TaskRequest', 'TaskRequest.sprint', 'TaskRequest.create', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?Sprint $sprint = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'requester_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.requester', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?User $requester = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'reviewer_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.reviewer', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?User $reviewer = null;

    #[ORM\Column(name: 'type', type: Types::STRING, length: 64, nullable: false, enumType: TaskRequestType::class)]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.type', 'TaskRequest.create', 'TaskRequest.show', 'TaskRequest.edit'])]
    private TaskRequestType $type = TaskRequestType::STATUS_CHANGE;

    #[ORM\Column(name: 'requested_status', type: Types::STRING, length: 64, nullable: true, enumType: TaskStatus::class)]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.requestedStatus', 'TaskRequest.create', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?TaskStatus $requestedStatus = null;

    #[ORM\Column(name: 'time', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.time', 'TaskRequest.create', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?DateTimeImmutable $time = null;

    #[ORM\Column(name: 'note', type: Types::TEXT, nullable: true)]
    #[Groups(['Sprint', 'TaskRequest', 'TaskRequest.note', 'TaskRequest.create', 'TaskRequest.show', 'TaskRequest.edit'])]
    private ?string $note = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getSprint(): ?Sprint
    {
        return $this->sprint;
    }

    public function setSprint(?Sprint $sprint): self
    {
        $this->sprint = $sprint;

        return $this;
    }

    public function getRequester(): ?User
    {
        return $this->requester;
    }

    public function setRequester(?User $requester): self
    {
        $this->requester = $requester;

        return $this;
    }

    public function getReviewer(): ?User
    {
        return $this->reviewer;
    }

    public function setReviewer(?User $reviewer): self
    {
        $this->reviewer = $reviewer;

        return $this;
    }

    public function getType(): TaskRequestType
    {
        return $this->type;
    }

    public function setType(TaskRequestType|string $type): self
    {
        $this->type = $type instanceof TaskRequestType ? $type : TaskRequestType::from($type);

        return $this;
    }

    public function getRequestedStatus(): ?TaskStatus
    {
        return $this->requestedStatus;
    }

    public function setRequestedStatus(TaskStatus|string|null $requestedStatus): self
    {
        if ($requestedStatus === null) {
            $this->requestedStatus = null;

            return $this;
        }

        $this->requestedStatus = $requestedStatus instanceof TaskStatus ? $requestedStatus : TaskStatus::from($requestedStatus);

        return $this;
    }

    public function getTime(): ?DateTimeImmutable
    {
        return $this->time;
    }

    public function setTime(?DateTimeImmutable $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
