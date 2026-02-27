<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\ResumeEducation;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeEducation as Entity;
use App\Recruit\Domain\Enum\ResumeEducationLevel;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\Recruit\Application\DTO\ResumeEducation
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ResumeEducation extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;
    #[Assert\NotBlank]
    protected string $schoolName = '';
    #[Assert\NotBlank]
    protected string $degree = '';
    protected ?string $fieldOfStudy = null;
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ResumeEducationLevel::class, 'getValues'])]
    protected string $level = ResumeEducationLevel::BACHELOR->value;
    #[Assert\NotNull]
    protected ?DateTimeImmutable $startDate = null;
    protected ?DateTimeImmutable $endDate = null;
    protected bool $isCurrent = false;
    protected ?string $description = null;
    protected int $sortOrder = 0;

    public function setResume(?Resume $resume): self
    {
        $this->setVisited('resume');
        $this->resume = $resume;

        return $this;
    }
    public function setSchoolName(string $schoolName): self
    {
        $this->setVisited('schoolName');
        $this->schoolName = $schoolName;

        return $this;
    }
    public function setDegree(string $degree): self
    {
        $this->setVisited('degree');
        $this->degree = $degree;

        return $this;
    }
    public function setFieldOfStudy(?string $fieldOfStudy): self
    {
        $this->setVisited('fieldOfStudy');
        $this->fieldOfStudy = $fieldOfStudy;

        return $this;
    }
    public function setLevel(string $level): self
    {
        $this->setVisited('level');
        $this->level = $level;

        return $this;
    }
    public function setStartDate(?DateTimeImmutable $startDate): self
    {
        $this->setVisited('startDate');
        $this->startDate = $startDate;

        return $this;
    }
    public function setEndDate(?DateTimeImmutable $endDate): self
    {
        $this->setVisited('endDate');
        $this->endDate = $endDate;

        return $this;
    }
    public function setIsCurrent(bool $isCurrent): self
    {
        $this->setVisited('isCurrent');
        $this->isCurrent = $isCurrent;

        return $this;
    }
    public function setDescription(?string $description): self
    {
        $this->setVisited('description');
        $this->description = $description;

        return $this;
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
            $this->schoolName = $entity->getSchoolName();
            $this->degree = $entity->getDegree();
            $this->fieldOfStudy = $entity->getFieldOfStudy();
            $this->level = $entity->getLevel()->value;
            $this->startDate = $entity->getStartDate();
            $this->endDate = $entity->getEndDate();
            $this->isCurrent = $entity->isCurrent();
            $this->description = $entity->getDescription();
            $this->sortOrder = $entity->getSortOrder();
        }

        return $this;
    }
}
