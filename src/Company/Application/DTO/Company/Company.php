<?php

declare(strict_types=1);

namespace App\Company\Application\DTO\Company;

use App\Company\Domain\Entity\Company as Entity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\DTO\Address;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Company
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class Company extends RestDto
{

    protected static array $mappings = [
        'mainAddress' => 'updateMainAddress',
    ];
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

    #[Assert\Valid]
    protected ?Address $mainAddress = null;

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

    public function getMainAddress(): ?Address
    {
        return $this->mainAddress;
    }

    public function setMainAddress(?Address $mainAddress): self
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
            $this->status = $entity->getStatus()->value;
            $this->mainAddress = Address::fromValueObject($entity->getMainAddress());
        }

        return $this;
    }
    protected function updateMainAddress(Entity $entity, ?Address $value): self
    {
        if ($value instanceof Address) {
            $entity->setMainAddress($value->toValueObject());
        }

        return $this;
    }

}
