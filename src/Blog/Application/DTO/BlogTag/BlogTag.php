<?php

declare(strict_types=1);

namespace App\Blog\Application\DTO\BlogTag;

use App\Blog\Domain\Entity\BlogTag as Entity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Blog\Application\DTO\BlogTag
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class BlogTag extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 128)]
    protected string $name = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 128)]
    protected string $slug = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->setVisited('name');
        $this->name = $name;

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

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->name = $entity->getName();
            $this->slug = $entity->getSlug();
        }

        return $this;
    }
}
