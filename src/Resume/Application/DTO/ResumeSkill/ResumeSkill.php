<?php

declare(strict_types=1);

namespace App\Resume\Application\DTO\ResumeSkill;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Resume\Domain\Entity\Resume;
use App\Resume\Domain\Entity\ResumeSkill as Entity;
use App\Resume\Domain\Enum\ResumeSkillLevel;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class ResumeSkill extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;
    #[Assert\NotBlank]
    protected string $name = '';
    #[Assert\NotBlank]
    #[Assert\Choice(callback: [ResumeSkillLevel::class, 'getValues'])]
    protected string $level = ResumeSkillLevel::INTERMEDIATE->value;
    protected ?int $yearsExperience = null;
    protected int $sortOrder = 0;

    public function setResume(?Resume $resume): self
    {
        $this->setVisited('resume');
        $this->resume = $resume;

        return $this;
    }
    public function setName(string $name): self
    {
        $this->setVisited('name');
        $this->name = $name;

        return $this;
    }
    public function setLevel(string $level): self
    {
        $this->setVisited('level');
        $this->level = $level;

        return $this;
    }
    public function setYearsExperience(?int $yearsExperience): self
    {
        $this->setVisited('yearsExperience');
        $this->yearsExperience = $yearsExperience;

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
            $this->name = $entity->getName();
            $this->level = $entity->getLevel()->value;
            $this->yearsExperience = $entity->getYearsExperience();
            $this->sortOrder = $entity->getSortOrder();
        }

        return $this;
    }
}
