<?php

declare(strict_types=1);

namespace App\Blog\Application\DTO\BlogComment;

use App\Blog\Domain\Entity\BlogComment as Entity;
use App\Blog\Domain\Entity\BlogPost;
use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\Blog\Application\DTO\BlogComment
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class BlogComment extends RestDto
{
    #[AppAssert\EntityReferenceExists(BlogPost::class)]
    protected ?BlogPost $post = null;

    #[Assert\NotBlank]
    protected string $content = '';

    #[Assert\Choice(callback: [BlogReferenceType::class, 'getValues'])]
    protected ?string $referenceType = null;

    #[Assert\Uuid]
    protected ?string $referenceId = null;

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->setVisited('post');
        $this->post = $post;

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

    public function getReferenceType(): ?string
    {
        return $this->referenceType;
    }

    public function setReferenceType(?string $referenceType): self
    {
        $this->setVisited('referenceType');
        $this->referenceType = $referenceType;

        return $this;
    }

    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    public function setReferenceId(?string $referenceId): self
    {
        $this->setVisited('referenceId');
        $this->referenceId = $referenceId;

        return $this;
    }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->post = $entity->getPost();
            $this->content = $entity->getContent();
            $this->referenceType = $entity->getReferenceType()?->value;
            $this->referenceId = $entity->getReferenceId();
        }

        return $this;
    }
}
