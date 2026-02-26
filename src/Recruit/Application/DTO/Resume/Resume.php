<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\Resume;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Resume as Entity;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 */
class Resume extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    protected string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 10000)]
    protected string $summary = '';

    #[Assert\Type('bool')]
    protected bool $isPublic = false;

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

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->setVisited('summary');
        $this->summary = $summary;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->setVisited('isPublic');
        $this->isPublic = $isPublic;

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
            $this->summary = $entity->getSummary();
            $this->isPublic = $entity->isPublic();
        }

        return $this;
    }
}
