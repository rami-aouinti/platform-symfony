<?php

declare(strict_types=1);

namespace App\Task\Domain\Entity;

use App\Company\Domain\Entity\Company;
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

/**
 * Project.
 *
 * @package App\Task\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'project')]
#[ORM\Index(name: 'idx_project_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_project_company_id', columns: ['company_id'])]
#[ORM\Index(name: 'idx_project_status', columns: ['status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Project implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Sprint', 'Project', 'Project.id', 'Project.show', 'Project.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Sprint', 'Project', 'Project.owner', 'Project.show', 'Project.edit'])]
    private ?User $owner = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Project', 'Project.company', 'Project.create', 'Project.show', 'Project.edit'])]
    private Company $company;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Sprint', 'Project', 'Project.name', 'Project.create', 'Project.show', 'Project.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['Sprint', 'Project', 'Project.description', 'Project.create', 'Project.show', 'Project.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: ProjectStatus::class)]
    #[Groups(['Sprint', 'Project', 'Project.status', 'Project.create', 'Project.show', 'Project.edit'])]
    private ProjectStatus $status = ProjectStatus::ACTIVE;

    #[ORM\Column(name: 'photo_url', type: Types::STRING, length: 2048, nullable: true)]
    #[Groups(['Sprint', 'Project', 'Project.photoUrl', 'Project.create', 'Project.show', 'Project.edit'])]
    private ?string $photoUrl = null;

    #[ORM\Column(name: 'photo_media_id', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['Sprint', 'Project', 'Project.photoMediaId', 'Project.create', 'Project.show', 'Project.edit'])]
    private ?string $photoMediaId = null;

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

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

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

    public function getPhotoMediaId(): ?string
    {
        return $this->photoMediaId;
    }

    public function setPhotoMediaId(?string $photoMediaId): self
    {
        $this->photoMediaId = $photoMediaId;

        return $this;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getStoredPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    #[Groups(['Project', 'Project.photoUrl', 'Project.show'])]
    public function getPhotoUrl(): string
    {
        return $this->photoUrl ?? sprintf(
            'https://ui-avatars.com/api/?name=%s&format=png',
            rawurlencode($this->name),
        );
    }

    #[Groups(['Project', 'Project.photo', 'Project.show'])]
    public function getPhoto(): string
    {
        return $this->getPhotoUrl();
    }

    #[Groups(['Project', 'Project.image', 'Project.show'])]
    public function getImage(): string
    {
        return $this->getPhotoUrl();
    }
}
