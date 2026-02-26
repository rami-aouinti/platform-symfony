<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Task\Domain\Enum\ProjectStatus;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'project')]
#[ORM\Index(name: 'idx_project_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_project_status', columns: ['status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Project implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Project', 'Project.id', 'Project.show', 'Project.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Project', 'Project.owner', 'Project.show', 'Project.edit'])]
    private ?User $owner = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Project', 'Project.name', 'Project.create', 'Project.show', 'Project.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['Project', 'Project.description', 'Project.create', 'Project.show', 'Project.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: ProjectStatus::class)]
    #[Groups(['Project', 'Project.status', 'Project.create', 'Project.show', 'Project.edit'])]
    private ProjectStatus $status = ProjectStatus::ACTIVE;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(ProjectStatus|string $status): self
    {
        $nextStatus = $status instanceof ProjectStatus ? $status : ProjectStatus::from($status);

        if (!$this->status->canTransitionTo($nextStatus) && $this->status !== $nextStatus) {
            return $this;
        }

        $this->status = $nextStatus;

        return $this;
    }
}
