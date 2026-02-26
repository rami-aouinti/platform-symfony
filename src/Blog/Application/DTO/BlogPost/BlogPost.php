<?php

declare(strict_types=1);

namespace App\Blog\Application\DTO\BlogPost;

use App\Blog\Domain\Entity\BlogPost as Entity;
use App\Blog\Domain\Enum\BlogPostStatus;
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
class BlogPost extends RestDto
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    protected string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    protected string $slug = '';

    protected ?string $excerpt = null;

    #[Assert\NotBlank]
    protected string $content = '';

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [BlogPostStatus::class, 'getValues'])]
    protected string $status = BlogPostStatus::DRAFT->value;

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

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->setVisited('excerpt');
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->setVisited('content');
        $this->content = $content;

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

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->title = $entity->getTitle();
            $this->slug = $entity->getSlug();
            $this->excerpt = $entity->getExcerpt();
            $this->content = $entity->getContent();
            $this->status = $entity->getStatus()->value;
        }

        return $this;
    }
}
