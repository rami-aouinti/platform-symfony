<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\ResumeProject;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeProject as Entity;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

class ResumeProject extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;

    #[Assert\NotBlank]
    protected string $name = '';

    protected ?string $description = null;
    #[Assert\Url]
    protected ?string $projectUrl = null;
    #[Assert\Url]
    protected ?string $repositoryUrl = null;
    protected ?DateTimeImmutable $startDate = null;
    protected ?DateTimeImmutable $endDate = null;
    protected int $sortOrder = 0;

    public function getResume(): ?Resume { return $this->resume; }
    public function setResume(?Resume $resume): self { $this->setVisited('resume'); $this->resume = $resume; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->setVisited('name'); $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->setVisited('description'); $this->description = $description; return $this; }
    public function getProjectUrl(): ?string { return $this->projectUrl; }
    public function setProjectUrl(?string $projectUrl): self { $this->setVisited('projectUrl'); $this->projectUrl = $projectUrl; return $this; }
    public function getRepositoryUrl(): ?string { return $this->repositoryUrl; }
    public function setRepositoryUrl(?string $repositoryUrl): self { $this->setVisited('repositoryUrl'); $this->repositoryUrl = $repositoryUrl; return $this; }
    public function getStartDate(): ?DateTimeImmutable { return $this->startDate; }
    public function setStartDate(?DateTimeImmutable $startDate): self { $this->setVisited('startDate'); $this->startDate = $startDate; return $this; }
    public function getEndDate(): ?DateTimeImmutable { return $this->endDate; }
    public function setEndDate(?DateTimeImmutable $endDate): self { $this->setVisited('endDate'); $this->endDate = $endDate; return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): self { $this->setVisited('sortOrder'); $this->sortOrder = $sortOrder; return $this; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->resume = $entity->getResume();
            $this->name = $entity->getName();
            $this->description = $entity->getDescription();
            $this->projectUrl = $entity->getProjectUrl();
            $this->repositoryUrl = $entity->getRepositoryUrl();
            $this->startDate = $entity->getStartDate();
            $this->endDate = $entity->getEndDate();
            $this->sortOrder = $entity->getSortOrder();
        }

        return $this;
    }
}
