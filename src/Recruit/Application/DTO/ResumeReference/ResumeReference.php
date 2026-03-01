<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\ResumeReference;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeReference as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

class ResumeReference extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;

    #[Assert\NotBlank]
    protected string $name = '';

    #[Assert\Email]
    protected ?string $contactEmail = null;

    protected ?string $relationName = null;
    protected ?string $contactPhone = null;
    protected int $sortOrder = 0;

    public function getResume(): ?Resume { return $this->resume; }
    public function setResume(?Resume $resume): self { $this->setVisited('resume'); $this->resume = $resume; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->setVisited('name'); $this->name = $name; return $this; }
    public function getRelationName(): ?string { return $this->relationName; }
    public function setRelationName(?string $relationName): self { $this->setVisited('relationName'); $this->relationName = $relationName; return $this; }
    public function getContactEmail(): ?string { return $this->contactEmail; }
    public function setContactEmail(?string $contactEmail): self { $this->setVisited('contactEmail'); $this->contactEmail = $contactEmail; return $this; }
    public function getContactPhone(): ?string { return $this->contactPhone; }
    public function setContactPhone(?string $contactPhone): self { $this->setVisited('contactPhone'); $this->contactPhone = $contactPhone; return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): self { $this->setVisited('sortOrder'); $this->sortOrder = $sortOrder; return $this; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->resume = $entity->getResume();
            $this->name = $entity->getName();
            $this->relationName = $entity->getRelationName();
            $this->contactEmail = $entity->getContactEmail();
            $this->contactPhone = $entity->getContactPhone();
            $this->sortOrder = $entity->getSortOrder();
        }

        return $this;
    }
}
