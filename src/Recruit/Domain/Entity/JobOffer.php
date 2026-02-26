<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Entity\Address as AddressValueObject;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Recruit\Domain\Enum\ApplicationType;
use App\Recruit\Domain\Enum\EmploymentType;
use App\Recruit\Domain\Enum\ExperienceLevel;
use App\Recruit\Domain\Enum\JobOfferStatus;
use App\Recruit\Domain\Enum\LanguageLevel;
use App\Recruit\Domain\Enum\RemoteMode;
use App\Recruit\Domain\Enum\SalaryCurrency;
use App\Recruit\Domain\Enum\WorkTime;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'job_offer')]
#[ORM\Index(name: 'idx_job_offer_company_status', columns: ['company_id', 'status'])]
#[ORM\Index(name: 'idx_job_offer_status', columns: ['status'])]
#[ORM\Index(name: 'idx_job_offer_published_at', columns: ['published_at'])]
#[ORM\Index(name: 'idx_job_offer_work_time', columns: ['work_time'])]
#[ORM\Index(name: 'idx_job_offer_employment_type', columns: ['employment_type'])]
#[ORM\Index(name: 'idx_job_offer_remote_mode', columns: ['remote_mode'])]
#[ORM\Index(name: 'idx_job_offer_experience_level', columns: ['experience_level'])]
#[ORM\Index(name: 'idx_job_offer_job_category_id', columns: ['job_category_id'])]
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

    #[ORM\Column(name: 'employment_type', type: Types::STRING, length: 64, nullable: false, enumType: EmploymentType::class)]
    #[Groups(['JobOffer', 'JobOffer.employmentType', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private EmploymentType $employmentType = EmploymentType::FULL_TIME;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: JobOfferStatus::class)]
    #[Groups(['JobOffer', 'JobOffer.status', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private JobOfferStatus $status = JobOfferStatus::DRAFT;

    #[ORM\Column(name: 'salary_min', type: Types::INTEGER, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryMin', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?int $salaryMin = null;

    #[ORM\Column(name: 'salary_max', type: Types::INTEGER, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryMax', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?int $salaryMax = null;

    #[ORM\Column(name: 'salary_currency', enumType: SalaryCurrency::class, type: Types::STRING, length: 3, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryCurrency', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?SalaryCurrency $salaryCurrency = null;

    #[ORM\Column(name: 'salary_period', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.salaryPeriod', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?string $salaryPeriod = null;

    #[ORM\Column(name: 'remote_mode', type: Types::STRING, length: 32, nullable: true, enumType: RemoteMode::class)]
    #[Groups(['JobOffer', 'JobOffer.remoteMode', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?RemoteMode $remoteMode = null;

    #[ORM\Column(name: 'experience_level', type: Types::STRING, length: 32, nullable: true, enumType: ExperienceLevel::class)]
    #[Groups(['JobOffer', 'JobOffer.experienceLevel', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?ExperienceLevel $experienceLevel = null;

    #[ORM\Column(name: 'work_time', type: Types::STRING, length: 32, nullable: true, enumType: WorkTime::class)]
    #[Groups(['JobOffer', 'JobOffer.workTime', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?WorkTime $workTime = null;

    #[ORM\Column(name: 'application_type', type: Types::STRING, length: 32, nullable: true, enumType: ApplicationType::class)]
    #[Groups(['JobOffer', 'JobOffer.applicationType', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?ApplicationType $applicationType = null;

    #[ORM\Column(name: 'published_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.publishedAt', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?DateTimeImmutable $publishedAt = null;

    #[ORM\Embedded(class: AddressValueObject::class, columnPrefix: false)]
    #[ORM\AttributeOverrides([
        new ORM\AttributeOverride(name: 'streetLine1', column: new ORM\Column(name: 'address_street_line_1', type: Types::STRING, length: 255, nullable: true)),
        new ORM\AttributeOverride(name: 'streetLine2', column: new ORM\Column(name: 'address_street_line_2', type: Types::STRING, length: 255, nullable: true)),
        new ORM\AttributeOverride(name: 'postalCode', column: new ORM\Column(name: 'address_postal_code', type: Types::STRING, length: 32, nullable: true)),
        new ORM\AttributeOverride(name: 'city', column: new ORM\Column(name: 'address_city', type: Types::STRING, length: 255, nullable: false)),
        new ORM\AttributeOverride(name: 'region', column: new ORM\Column(name: 'address_region', type: Types::STRING, length: 255, nullable: true)),
        new ORM\AttributeOverride(name: 'countryCode', column: new ORM\Column(name: 'address_country_code', type: Types::STRING, length: 2, nullable: false)),
    ])]
    #[Groups(['JobOffer', 'JobOffer.address', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private AddressValueObject $address;

    #[ORM\ManyToOne(targetEntity: JobCategory::class)]
    #[ORM\JoinColumn(name: 'job_category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['JobOffer', 'JobOffer.jobCategory', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?JobCategory $jobCategory = null;

    #[ORM\Column(name: 'language_level', enumType: LanguageLevel::class, type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['JobOffer', 'JobOffer.languageLevel', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private ?LanguageLevel $languageLevel = null;

    /**
     * @var Collection<int, Skill>
     */
    #[ORM\ManyToMany(targetEntity: Skill::class)]
    #[ORM\JoinTable(name: 'job_offer_skill')]
    #[ORM\JoinColumn(name: 'job_offer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'skill_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobOffer', 'JobOffer.skills', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private Collection $skills;

    /**
     * @var Collection<int, Language>
     */
    #[ORM\ManyToMany(targetEntity: Language::class)]
    #[ORM\JoinTable(name: 'job_offer_language')]
    #[ORM\JoinColumn(name: 'job_offer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'language_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobOffer', 'JobOffer.languages', 'JobOffer.create', 'JobOffer.show', 'JobOffer.edit'])]
    private Collection $languages;

    /**
     * @var Collection<int, JobApplication>|null
     */
    #[ORM\OneToMany(targetEntity: JobApplication::class, mappedBy: 'jobOffer')]
    #[Groups(['JobOffer', 'JobOffer.jobApplications', 'JobOffer.show'])]
    private ?Collection $jobApplications = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->skills = new ArrayCollection();
        $this->languages = new ArrayCollection();
        $this->jobApplications = new ArrayCollection();
        $this->address = new AddressValueObject();
    }

    /**
     * @return Collection<int, JobApplication>|null
     */
    public function getJobApplications(): ?Collection
    {
        return $this->jobApplications;
    }

    /**
     * @param Collection<int, JobApplication>|null $jobApplications
     */
    public function setJobApplications(?Collection $jobApplications): self
    {
        $this->jobApplications = $jobApplications;

        return $this;
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
        return $this->employmentType->value;
    }

    public function setEmploymentType(EmploymentType|string $employmentType): self
    {
        $this->employmentType = $employmentType instanceof EmploymentType
            ? $employmentType
            : EmploymentType::from($employmentType);

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status->value;
    }

    public function setStatus(JobOfferStatus|string $status): self
    {
        $this->status = $status instanceof JobOfferStatus
            ? $status
            : JobOfferStatus::from($status);

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
        return $this->salaryCurrency?->value;
    }

    public function setSalaryCurrency(SalaryCurrency|string|null $salaryCurrency): self
    {
        $this->salaryCurrency = $salaryCurrency instanceof SalaryCurrency
            ? $salaryCurrency
            : ($salaryCurrency !== null ? SalaryCurrency::from($salaryCurrency) : null);

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

    public function getRemoteMode(): ?string
    {
        return $this->remoteMode?->value;
    }

    public function setRemoteMode(RemoteMode|string|null $remoteMode): self
    {
        $this->remoteMode = $remoteMode instanceof RemoteMode
            ? $remoteMode
            : ($remoteMode !== null ? RemoteMode::from($remoteMode) : null);

        return $this;
    }

    public function getRemotePolicy(): ?string
    {
        return $this->getRemoteMode();
    }

    public function setRemotePolicy(RemoteMode|string|null $remotePolicy): self
    {
        $this->setRemoteMode($remotePolicy);

        return $this;
    }

    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel?->value;
    }

    public function setExperienceLevel(ExperienceLevel|string|null $experienceLevel): self
    {
        $this->experienceLevel = $experienceLevel instanceof ExperienceLevel
            ? $experienceLevel
            : ($experienceLevel !== null ? ExperienceLevel::from($experienceLevel) : null);

        return $this;
    }

    public function getWorkTime(): ?string
    {
        return $this->workTime?->value;
    }

    public function setWorkTime(WorkTime|string|null $workTime): self
    {
        $this->workTime = $workTime instanceof WorkTime
            ? $workTime
            : ($workTime !== null ? WorkTime::from($workTime) : null);

        return $this;
    }

    public function getApplicationType(): ?string
    {
        return $this->applicationType?->value;
    }

    public function setApplicationType(ApplicationType|string|null $applicationType): self
    {
        $this->applicationType = $applicationType instanceof ApplicationType
            ? $applicationType
            : ($applicationType !== null ? ApplicationType::from($applicationType) : null);

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

    public function getAddress(): AddressValueObject
    {
        return $this->address;
    }

    public function setAddress(AddressValueObject $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getJobCategory(): ?JobCategory
    {
        return $this->jobCategory;
    }

    public function setJobCategory(?JobCategory $jobCategory): self
    {
        $this->jobCategory = $jobCategory;

        return $this;
    }

    public function getLanguageLevel(): ?string
    {
        return $this->languageLevel?->value;
    }

    public function setLanguageLevel(LanguageLevel|string|null $languageLevel): self
    {
        $this->languageLevel = $languageLevel instanceof LanguageLevel
            ? $languageLevel
            : ($languageLevel !== null ? LanguageLevel::from($languageLevel) : null);

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    /**
     * @param array<int, Skill> $skills
     */
    public function setSkills(array $skills): self
    {
        $this->skills = new ArrayCollection($skills);

        return $this;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function clearSkills(): self
    {
        $this->skills->clear();

        return $this;
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    /**
     * @param array<int, Language> $languages
     */
    public function setLanguages(array $languages): self
    {
        $this->languages = new ArrayCollection($languages);

        return $this;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages->add($language);
        }

        return $this;
    }

    public function clearLanguages(): self
    {
        $this->languages->clear();

        return $this;
    }
}
