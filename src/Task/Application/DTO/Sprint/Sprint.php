<?php

declare(strict_types=1);

namespace App\Task\Application\DTO\Sprint;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Domain\Entity\Sprint as Entity;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Sprint extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\DateTime]
    protected ?string $startDate = null;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    protected ?string $endDate = null;

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function setStartDate(?string $startDate): self
    {
        $this->setVisited('startDate');
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->endDate;
    }

    public function setEndDate(?string $endDate): self
    {
        $this->setVisited('endDate');
        $this->endDate = $endDate;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->startDate = $entity->getStartDate()?->format(DateTimeImmutable::ATOM);
            $this->endDate = $entity->getEndDate()?->format(DateTimeImmutable::ATOM);
        }

        return $this;
    }
}
