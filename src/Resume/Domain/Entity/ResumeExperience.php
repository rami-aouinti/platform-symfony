<?php

declare(strict_types=1);

namespace App\Resume\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Resume\Domain\Enum\ResumeEmploymentType;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'resume_experience')]
#[ORM\Index(name: 'idx_resume_experience_resume_sort', columns: ['resume_id', 'sort_order'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class ResumeExperience implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['ResumeExperience', 'ResumeExperience.id', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Resume::class)]
    #[ORM\JoinColumn(name: 'resume_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['ResumeExperience', 'ResumeExperience.resume', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private ?Resume $resume = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255)]
    #[Groups(['ResumeExperience', 'ResumeExperience.title', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private string $title = '';

    #[ORM\Column(name: 'company_name', type: Types::STRING, length: 255)]
    #[Groups(['ResumeExperience', 'ResumeExperience.companyName', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private string $companyName = '';

    #[ORM\Column(name: 'employment_type', type: Types::STRING, length: 32, enumType: ResumeEmploymentType::class)]
    #[Groups(['ResumeExperience', 'ResumeExperience.employmentType', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private ResumeEmploymentType $employmentType = ResumeEmploymentType::FULL_TIME;

    #[ORM\Column(name: 'start_date', type: Types::DATE_IMMUTABLE)]
    #[Groups(['ResumeExperience', 'ResumeExperience.startDate', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private ?DateTimeImmutable $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['ResumeExperience', 'ResumeExperience.endDate', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private ?DateTimeImmutable $endDate = null;

    #[ORM\Column(name: 'is_current', type: Types::BOOLEAN, options: [
        'default' => false,
    ])]
    #[Groups(['ResumeExperience', 'ResumeExperience.isCurrent', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private bool $isCurrent = false;

    #[ORM\Column(name: 'location', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['ResumeExperience', 'ResumeExperience.location', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private ?string $location = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['ResumeExperience', 'ResumeExperience.description', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'sort_order', type: Types::INTEGER, options: [
        'default' => 0,
    ])]
    #[Groups(['ResumeExperience', 'ResumeExperience.sortOrder', 'ResumeExperience.create', 'ResumeExperience.show', 'ResumeExperience.edit'])]
    private int $sortOrder = 0;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }
    public function getResume(): ?Resume
    {
        return $this->resume;
    }
    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;

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
    public function getCompanyName(): string
    {
        return $this->companyName;
    }
    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }
    public function getEmploymentType(): ResumeEmploymentType
    {
        return $this->employmentType;
    }
    public function setEmploymentType(ResumeEmploymentType|string $employmentType): self
    {
        $this->employmentType = $employmentType instanceof ResumeEmploymentType ? $employmentType : ResumeEmploymentType::from($employmentType);

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
    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }
    public function setIsCurrent(bool $isCurrent): self
    {
        $this->isCurrent = $isCurrent;

        return $this;
    }
    public function getLocation(): ?string
    {
        return $this->location;
    }
    public function setLocation(?string $location): self
    {
        $this->location = $location;

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
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
