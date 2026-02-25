<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'job_offer')]
#[ORM\Index(name: 'idx_job_offer_company_status', columns: ['company_id', 'status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class JobOffer implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['JobOffer', 'JobOffer.id', 'JobOffer.show', 'JobOffer.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobOffer', 'JobOffer.company', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['JobOffer.createdBy', 'JobOffer.show'])]
    private ?User $createdBy = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['JobOffer', 'JobOffer.title', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private string $title = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: false)]
    #[Groups(['JobOffer', 'JobOffer.description', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private string $description = '';

    #[ORM\Column(name: 'location', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['JobOffer', 'JobOffer.location', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private string $location = '';

    #[ORM\Column(name: 'employment_type', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['JobOffer', 'JobOffer.employmentType', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private string $employmentType = '';

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['JobOffer', 'JobOffer.status', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private string $status = 'draft';

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getEmploymentType(): string
    {
        return $this->employmentType;
    }

    public function setEmploymentType(string $employmentType): self
    {
        $this->employmentType = $employmentType;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
