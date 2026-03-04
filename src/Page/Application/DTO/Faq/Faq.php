<?php

declare(strict_types=1);

namespace App\Page\Application\DTO\Faq;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Page\Domain\Entity\Faq as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Faq extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 255)]
    protected string $name = '';

    #[Assert\NotBlank]
    #[Assert\NotNull]
    protected string $description = '';

    #[Assert\NotNull]
    #[Assert\Type('integer')]
    protected int $order = 0;

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->setVisited('name'); $this->name = $name; return $this; }
    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): self { $this->setVisited('description'); $this->description = $description; return $this; }
    public function getOrder(): int { return $this->order; }
    public function setOrder(int $order): self { $this->setVisited('order'); $this->order = $order; return $this; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->name = $entity->getName();
            $this->description = $entity->getDescription();
            $this->order = $entity->getOrder();
        }

        return $this;
    }
}
