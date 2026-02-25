<?php

declare(strict_types=1);

namespace App\JobOffer\Application\DTO\JobOffer;

use App\Company\Domain\Entity\Company;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\JobOffer\Domain\Entity\JobOffer as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class JobOffer extends RestDto
{
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
    #[Assert\Length(min: 2, max: 64)]
    protected string $employmentType = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Choice(choices: ['draft', 'open', 'closed'])]
    protected string $status = 'draft';

    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(Company::class)]
    protected ?Company $company = null;

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
            $this->company = $entity->getCompany();
        }

        return $this;
    }
}
