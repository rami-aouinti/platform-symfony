<?php

declare(strict_types=1);

namespace App\Company\Application\DTO\Company;

use App\Company\Domain\Entity\Company as Entity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Company extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $legalName = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $slug = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 64)]
    protected string $status = 'active';

    protected ?string $mainAddress = null;

    public function getLegalName(): string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): self
    {
        $this->setVisited('legalName');
        $this->legalName = $legalName;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->setVisited('slug');
        $this->slug = $slug;

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

    public function getMainAddress(): ?string
    {
        return $this->mainAddress;
    }

    public function setMainAddress(?string $mainAddress): self
    {
        $this->setVisited('mainAddress');
        $this->mainAddress = $mainAddress;

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
            $this->legalName = $entity->getLegalName();
            $this->slug = $entity->getSlug();
            $this->status = $entity->getStatus();
            $this->mainAddress = $entity->getMainAddress();
        }

        return $this;
    }
}
