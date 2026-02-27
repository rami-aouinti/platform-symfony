<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\JobOffer;

use App\Company\Domain\Entity\Company;
use App\General\Application\DTO\Address;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\JobCategory;
use App\Recruit\Domain\Entity\JobOffer as Entity;
use App\Recruit\Domain\Entity\Language;
use App\Recruit\Domain\Entity\Skill;
use App\Recruit\Domain\Enum\ApplicationType;
use App\Recruit\Domain\Enum\EmploymentType;
use App\Recruit\Domain\Enum\ExperienceLevel;
use App\Recruit\Domain\Enum\JobOfferStatus;
use App\Recruit\Domain\Enum\LanguageLevel;
use App\Recruit\Domain\Enum\RemoteMode;
use App\Recruit\Domain\Enum\SalaryCurrency;
use App\Recruit\Domain\Enum\WorkTime;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

use function array_map;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Recruit\Application\DTO\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class JobOffer extends RestDto
{
    private const SALARY_PERIODS = ['hourly', 'daily', 'monthly', 'yearly'];
    /**
     * @var array<string, string>
     */
    protected static array $mappings = [
        'skills' => 'updateSkills',
        'languages' => 'updateLanguages',
        'address' => 'updateAddress',
    ];

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $title = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected string $description = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $location = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [EmploymentType::class, 'getValues'])]
    #[Assert\Length(min: 2, max: 64)]
    protected string $employmentType = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [JobOfferStatus::class, 'getValues'])]
    protected string $status = 'draft';

    #[Assert\Range(min: 0, max: 10000000)]
    protected ?int $salaryMin = null;

    #[Assert\Range(min: 0, max: 10000000)]
    protected ?int $salaryMax = null;

    #[Assert\Choice(callback: [SalaryCurrency::class, 'getValues'])]
    protected ?string $salaryCurrency = null;

    #[Assert\Choice(choices: self::SALARY_PERIODS)]
    protected ?string $salaryPeriod = null;

    #[Assert\Choice(callback: [RemoteMode::class, 'getValues'])]
    protected ?string $remoteMode = null;

    #[Assert\Choice(callback: [ExperienceLevel::class, 'getValues'])]
    protected ?string $experienceLevel = null;

    #[Assert\Choice(callback: [WorkTime::class, 'getValues'])]
    protected ?string $workTime = null;

    #[Assert\Choice(callback: [ApplicationType::class, 'getValues'])]
    protected ?string $applicationType = null;

    #[Assert\Type(DateTimeImmutable::class)]
    protected ?DateTimeImmutable $publishedAt = null;

    #[AppAssert\EntityReferenceExists(JobCategory::class)]
    protected ?JobCategory $jobCategory = null;

    #[Assert\Valid]
    protected ?Address $address = null;

    #[Assert\Choice(callback: [LanguageLevel::class, 'getValues'])]
    protected ?string $languageLevel = null;

    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Company::class)]
    protected ?Company $company = null;

    /**
     * @var array<int, Skill>
     */
    #[AppAssert\EntityReferenceExists(Skill::class)]
    protected array $skills = [];

    /**
     * @var array<int, Language>
     */
    #[AppAssert\EntityReferenceExists(Language::class)]
    protected array $languages = [];

    #[Assert\Expression(
        'this.getSalaryMin() === null or this.getSalaryMax() === null or this.getSalaryMax() >= this.getSalaryMin()',
        message: 'salaryMax must be greater than or equal to salaryMin.'
    )]
    #[Assert\Expression(
        '(this.getSalaryMin() === null and this.getSalaryMax() === null) or (this.getSalaryCurrency() !== null and this.getSalaryPeriod() !== null)',
        message: 'salaryCurrency and salaryPeriod are required when salaryMin or salaryMax is provided.'
    )]
    #[Assert\Expression(
        'this.getPublishedAt() === null or this.getStatus() !== "draft"',
        message: 'Draft job offers cannot define publishedAt.'
    )]
    private bool $consistency = true;

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): self
    {
        $this->setVisited('title');
        $this->title = $title;

        return $this;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->setVisited('description');
        $this->description = $description;

        return $this;
    }
    public function getLocation(): string
    {
        return $this->location;
    }
    public function setLocation(string $location): self
    {
        $this->setVisited('location');
        $this->location = $location;

        return $this;
    }
    public function getEmploymentType(): string
    {
        return $this->employmentType;
    }
    public function setEmploymentType(string $employmentType): self
    {
        $this->setVisited('employmentType');
        $this->employmentType = $employmentType;

        return $this;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): self
    {
        $this->setVisited('status');
        $this->status = $status;

        return $this;
    }
    public function getSalaryMin(): ?int
    {
        return $this->salaryMin;
    }
    public function setSalaryMin(?int $salaryMin): self
    {
        $this->setVisited('salaryMin');
        $this->salaryMin = $salaryMin;

        return $this;
    }
    public function getSalaryMax(): ?int
    {
        return $this->salaryMax;
    }
    public function setSalaryMax(?int $salaryMax): self
    {
        $this->setVisited('salaryMax');
        $this->salaryMax = $salaryMax;

        return $this;
    }
    public function getSalaryCurrency(): ?string
    {
        return $this->salaryCurrency;
    }
    public function setSalaryCurrency(?string $salaryCurrency): self
    {
        $this->setVisited('salaryCurrency');
        $this->salaryCurrency = $salaryCurrency;

        return $this;
    }
    public function getSalaryPeriod(): ?string
    {
        return $this->salaryPeriod;
    }
    public function setSalaryPeriod(?string $salaryPeriod): self
    {
        $this->setVisited('salaryPeriod');
        $this->salaryPeriod = $salaryPeriod;

        return $this;
    }
    public function getRemoteMode(): ?string
    {
        return $this->remoteMode;
    }
    public function setRemoteMode(?string $remoteMode): self
    {
        $this->setVisited('remoteMode');
        $this->remoteMode = $remoteMode;

        return $this;
    }
    public function getRemotePolicy(): ?string
    {
        return $this->getRemoteMode();
    }
    public function setRemotePolicy(?string $remotePolicy): self
    {
        return $this->setRemoteMode($remotePolicy);
    }
    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel;
    }
    public function setExperienceLevel(?string $experienceLevel): self
    {
        $this->setVisited('experienceLevel');
        $this->experienceLevel = $experienceLevel;

        return $this;
    }
    public function getWorkTime(): ?string
    {
        return $this->workTime;
    }
    public function setWorkTime(?string $workTime): self
    {
        $this->setVisited('workTime');
        $this->workTime = $workTime;

        return $this;
    }
    public function getApplicationType(): ?string
    {
        return $this->applicationType;
    }
    public function setApplicationType(?string $applicationType): self
    {
        $this->setVisited('applicationType');
        $this->applicationType = $applicationType;

        return $this;
    }
    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }
    public function setPublishedAt(?DateTimeImmutable $publishedAt): self
    {
        $this->setVisited('publishedAt');
        $this->publishedAt = $publishedAt;

        return $this;
    }
    public function getAddress(): ?Address
    {
        return $this->address;
    }
    public function setAddress(?Address $address): self
    {
        $this->setVisited('address');
        $this->address = $address;

        return $this;
    }
    public function getJobCategory(): ?JobCategory
    {
        return $this->jobCategory;
    }
    public function setJobCategory(?JobCategory $jobCategory): self
    {
        $this->setVisited('jobCategory');
        $this->jobCategory = $jobCategory;

        return $this;
    }
    public function getLanguageLevel(): ?string
    {
        return $this->languageLevel;
    }
    public function setLanguageLevel(?string $languageLevel): self
    {
        $this->setVisited('languageLevel');
        $this->languageLevel = $languageLevel;

        return $this;
    }
    public function getCompany(): ?Company
    {
        return $this->company;
    }
    public function setCompany(?Company $company): self
    {
        $this->setVisited('company');
        $this->company = $company;

        return $this;
    }

    /**
     * @return array<int, Skill>
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @param array<int, Skill> $skills
     */
    public function setSkills(array $skills): self
    {
        $this->setVisited('skills');
        $this->skills = $skills;

        return $this;
    }

    /**
     * @return array<int, Language>
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param array<int, Language> $languages
     */
    public function setLanguages(array $languages): self
    {
        $this->setVisited('languages');
        $this->languages = $languages;

        return $this;
    }

    /**
     * @param EntityInterface|Entity $entity
     */
    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->title = $entity->getTitle();
            $this->description = $entity->getDescription();
            $this->location = $entity->getLocation();
            $this->employmentType = $entity->getEmploymentType();
            $this->status = $entity->getStatus();
            $this->salaryMin = $entity->getSalaryMin();
            $this->salaryMax = $entity->getSalaryMax();
            $this->salaryCurrency = $entity->getSalaryCurrency();
            $this->salaryPeriod = $entity->getSalaryPeriod();
            $this->remoteMode = $entity->getRemoteMode();
            $this->experienceLevel = $entity->getExperienceLevel();
            $this->workTime = $entity->getWorkTime();
            $this->applicationType = $entity->getApplicationType();
            $this->publishedAt = $entity->getPublishedAt();
            $this->address = Address::fromValueObject($entity->getAddress());
            $this->jobCategory = $entity->getJobCategory();
            $this->languageLevel = $entity->getLanguageLevel();
            $this->company = $entity->getCompany();
            $this->skills = $entity->getSkills()->toArray();
            $this->languages = $entity->getLanguages()->toArray();
        }

        return $this;
    }

    protected function updateAddress(Entity $entity, ?Address $value): self
    {
        if ($value instanceof Address) {
            $entity->setAddress($value->toValueObject());
        }

        return $this;
    }

    /**
     * @param array<int, Skill> $value
     */
    protected function updateSkills(Entity $entity, array $value): self
    {
        $entity->clearSkills();
        array_map(static fn (Skill $skill): Entity => $entity->addSkill($skill), $value);

        return $this;
    }

    /**
     * @param array<int, Language> $value
     */
    protected function updateLanguages(Entity $entity, array $value): self
    {
        $entity->clearLanguages();
        array_map(static fn (Language $language): Entity => $entity->addLanguage($language), $value);

        return $this;
    }
}
