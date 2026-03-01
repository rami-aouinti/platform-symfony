<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\ResumeLanguage;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeLanguage as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

class ResumeLanguage extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;

    #[Assert\NotBlank]
    protected string $name = '';

    protected ?string $level = null;
    protected int $sortOrder = 0;

    public function getResume(): ?Resume { return $this->resume; }
    public function setResume(?Resume $resume): self { $this->setVisited('resume'); $this->resume = $resume; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->setVisited('name'); $this->name = $name; return $this; }
    public function getLevel(): ?string { return $this->level; }
    public function setLevel(?string $level): self { $this->setVisited('level'); $this->level = $level; return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): self { $this->setVisited('sortOrder'); $this->sortOrder = $sortOrder; return $this; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->resume = $entity->getResume();
            $this->name = $entity->getName();
            $this->level = $entity->getLevel();
            $this->sortOrder = $entity->getSortOrder();
        }

        return $this;
    }
}
