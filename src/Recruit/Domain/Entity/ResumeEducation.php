<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Recruit\Domain\Enum\ResumeEducationLevel;
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
#[ORM\Table(name: 'resume_education')]
#[ORM\Index(name: 'idx_resume_education_resume_sort', columns: ['resume_id', 'sort_order'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class ResumeEducation implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['ResumeEducation', 'ResumeEducation.id', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Resume::class)]
    #[ORM\JoinColumn(name: 'resume_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['ResumeEducation', 'ResumeEducation.resume', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private ?Resume $resume = null;

    #[ORM\Column(name: 'school_name', type: Types::STRING, length: 255)]
    #[Groups(['ResumeEducation', 'ResumeEducation.schoolName', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private string $schoolName = '';

    #[ORM\Column(name: 'degree', type: Types::STRING, length: 255)]
    #[Groups(['ResumeEducation', 'ResumeEducation.degree', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private string $degree = '';

    #[ORM\Column(name: 'field_of_study', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['ResumeEducation', 'ResumeEducation.fieldOfStudy', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private ?string $fieldOfStudy = null;

    #[ORM\Column(name: 'level', type: Types::STRING, length: 32, enumType: ResumeEducationLevel::class)]
    #[Groups(['ResumeEducation', 'ResumeEducation.level', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private ResumeEducationLevel $level = ResumeEducationLevel::BACHELOR;

    #[ORM\Column(name: 'start_date', type: Types::DATE_IMMUTABLE)]
    #[Groups(['ResumeEducation', 'ResumeEducation.startDate', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private ?DateTimeImmutable $startDate = null;

    #[ORM\Column(name: 'end_date', type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups(['ResumeEducation', 'ResumeEducation.endDate', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private ?DateTimeImmutable $endDate = null;

    #[ORM\Column(name: 'is_current', type: Types::BOOLEAN, options: [
        'default' => false,
    ])]
    #[Groups(['ResumeEducation', 'ResumeEducation.isCurrent', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private bool $isCurrent = false;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['ResumeEducation', 'ResumeEducation.description', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'sort_order', type: Types::INTEGER, options: [
        'default' => 0,
    ])]
    #[Groups(['ResumeEducation', 'ResumeEducation.sortOrder', 'ResumeEducation.create', 'ResumeEducation.show', 'ResumeEducation.edit'])]
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
    public function getSchoolName(): string
    {
        return $this->schoolName;
    }
    public function setSchoolName(string $schoolName): self
    {
        $this->schoolName = $schoolName;

        return $this;
    }
    public function getDegree(): string
    {
        return $this->degree;
    }
    public function setDegree(string $degree): self
    {
        $this->degree = $degree;

        return $this;
    }
    public function getFieldOfStudy(): ?string
    {
        return $this->fieldOfStudy;
    }
    public function setFieldOfStudy(?string $fieldOfStudy): self
    {
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }
    public function getLevel(): ResumeEducationLevel
    {
        return $this->level;
    }
    public function setLevel(ResumeEducationLevel|string $level): self
    {
        $this->level = $level instanceof ResumeEducationLevel ? $level : ResumeEducationLevel::from($level);

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
