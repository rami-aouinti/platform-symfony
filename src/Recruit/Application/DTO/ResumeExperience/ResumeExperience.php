<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\ResumeExperience;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeExperience as Entity;
use App\Recruit\Domain\Enum\ResumeEmploymentType;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ResumeExperience extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;

    #[Assert\NotBlank]
    protected string $title = '';

    #[Assert\NotBlank]
    protected string $companyName = '';

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ResumeEmploymentType::class, 'getValues'])]
    protected string $employmentType = ResumeEmploymentType::FULL_TIME->value;

    #[Assert\NotNull]
    protected ?DateTimeImmutable $startDate = null;

    protected ?DateTimeImmutable $endDate = null;
    protected bool $isCurrent = false;
    protected ?string $location = null;
    protected ?string $description = null;
    protected int $sortOrder = 0;

    public function getResume(): ?Resume
    {
        return $this->resume;
    }
    public function setResume(?Resume $resume): self
    {
        $this->setVisited('resume');
        $this->resume = $resume;

        return $this;
    }
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
    public function getCompanyName(): string
    {
        return $this->companyName;
    }
    public function setCompanyName(string $companyName): self
    {
        $this->setVisited('companyName');
        $this->companyName = $companyName;

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
    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }
    public function setStartDate(?DateTimeImmutable $startDate): self
    {
        $this->setVisited('startDate');
        $this->startDate = $startDate;

        return $this;
    }
    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }
    public function setEndDate(?DateTimeImmutable $endDate): self
    {
        $this->setVisited('endDate');
        $this->endDate = $endDate;

        return $this;
    }
    public function isCurrent(): bool
    {
        return $this->isCurrent;
    }
    public function setIsCurrent(bool $isCurrent): self
    {
        $this->setVisited('isCurrent');
        $this->isCurrent = $isCurrent;

        return $this;
    }
    public function getLocation(): ?string
    {
        return $this->location;
    }
    public function setLocation(?string $location): self
    {
        $this->setVisited('location');
        $this->location = $location;

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): self
    {
        $this->setVisited('description');
        $this->description = $description;

        return $this;
    }
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }
    public function setSortOrder(int $sortOrder): self
    {
        $this->setVisited('sortOrder');
        $this->sortOrder = $sortOrder;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->resume = $entity->getResume();
            $this->title = $entity->getTitle();
            $this->companyName = $entity->getCompanyName();
            $this->employmentType = $entity->getEmploymentType()->value;
            $this->startDate = $entity->getStartDate();
            $this->endDate = $entity->getEndDate();
            $this->isCurrent = $entity->isCurrent();
            $this->location = $entity->getLocation();
            $this->description = $entity->getDescription();
            $this->sortOrder = $entity->getSortOrder();
        }

        return $this;
    }
}
