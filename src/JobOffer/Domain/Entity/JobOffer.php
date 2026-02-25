<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'job_offer')]
#[ORM\Index(name: 'idx_job_offer_company_status', columns: ['company_id', 'status'])]
#[ORM\Index(name: 'idx_job_offer_published_at', columns: ['published_at'])]
#[ORM\Index(name: 'idx_job_offer_work_time', columns: ['work_time'])]
#[ORM\Index(name: 'idx_job_offer_employment_type', columns: ['employment_type'])]
#[ORM\Index(name: 'idx_job_offer_remote_policy', columns: ['remote_policy'])]
#[ORM\Index(name: 'idx_job_offer_city', columns: ['city'])]
#[ORM\Index(name: 'idx_job_offer_region', columns: ['region'])]
#[ORM\Index(name: 'idx_job_offer_salary_min', columns: ['salary_min'])]
#[ORM\Index(name: 'idx_job_offer_salary_max', columns: ['salary_max'])]
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

    #[ORM\Column(name: 'salary_min', type: Types::INTEGER, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryMin', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?int $salaryMin = null;

    #[ORM\Column(name: 'salary_max', type: Types::INTEGER, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryMax', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?int $salaryMax = null;

    #[ORM\Column(name: 'salary_currency', type: Types::STRING, length: 3, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryCurrency', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $salaryCurrency = null;

    #[ORM\Column(name: 'salary_period', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryPeriod', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $salaryPeriod = null;

    #[ORM\Column(name: 'remote_policy', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.remotePolicy', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $remotePolicy = null;

    #[ORM\Column(name: 'experience_level', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.experienceLevel', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $experienceLevel = null;

    #[ORM\Column(name: 'work_time', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.workTime', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $workTime = null;

    #[ORM\Column(name: 'application_type', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.applicationType', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $applicationType = null;

    #[ORM\Column(name: 'published_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.publishedAt', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?DateTimeImmutable $publishedAt = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 128, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.city', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $city = null;

    #[ORM\Column(name: 'region', type: Types::STRING, length: 128, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.region', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $region = null;

    #[ORM\Column(name: 'country', type: Types::STRING, length: 2, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.country', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $country = null;

    #[ORM\Column(name: 'language_level', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.languageLevel', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $languageLevel = null;

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

    public function getSalaryMin(): ?int
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(?int $salaryMin): self
    {
        $this->salaryMin = $salaryMin;

        return $this;
    }

    public function getSalaryMax(): ?int
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(?int $salaryMax): self
    {
        $this->salaryMax = $salaryMax;

        return $this;
    }

    public function getSalaryCurrency(): ?string
    {
        return $this->salaryCurrency;
    }

    public function setSalaryCurrency(?string $salaryCurrency): self
    {
        $this->salaryCurrency = $salaryCurrency;

        return $this;
    }

    public function getSalaryPeriod(): ?string
    {
        return $this->salaryPeriod;
    }

    public function setSalaryPeriod(?string $salaryPeriod): self
    {
        $this->salaryPeriod = $salaryPeriod;

        return $this;
    }

    public function getRemotePolicy(): ?string
    {
        return $this->remotePolicy;
    }

    public function setRemotePolicy(?string $remotePolicy): self
    {
        $this->remotePolicy = $remotePolicy;

        return $this;
    }

    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(?string $experienceLevel): self
    {
        $this->experienceLevel = $experienceLevel;

        return $this;
    }

    public function getWorkTime(): ?string
    {
        return $this->workTime;
    }

    public function setWorkTime(?string $workTime): self
    {
        $this->workTime = $workTime;

        return $this;
    }

    public function getApplicationType(): ?string
    {
        return $this->applicationType;
    }

    public function setApplicationType(?string $applicationType): self
    {
        $this->applicationType = $applicationType;

        return $this;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getLanguageLevel(): ?string
    {
        return $this->languageLevel;
    }

    public function setLanguageLevel(?string $languageLevel): self
    {
        $this->languageLevel = $languageLevel;

        return $this;
    }
}
