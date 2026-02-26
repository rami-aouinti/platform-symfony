<?php

declare(strict_types=1);

namespace App\Blog\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'blog_tag')]
#[ORM\UniqueConstraint(name: 'uq_blog_tag_slug', columns: ['slug'])]
#[ORM\UniqueConstraint(name: 'uq_blog_tag_name', columns: ['name'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class BlogTag implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['BlogTag', 'BlogTag.id', 'BlogTag.show', 'BlogTag.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128, nullable: false)]
    #[Groups(['BlogTag', 'BlogTag.name', 'BlogTag.create', 'BlogTag.show', 'BlogTag.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'slug', type: Types::STRING, length: 128, nullable: false)]
    #[Groups(['BlogTag', 'BlogTag.slug', 'BlogTag.create', 'BlogTag.show', 'BlogTag.edit'])]
    private string $slug = '';

    /**
     * @var Collection<int, BlogPost>
     */
    #[ORM\ManyToMany(targetEntity: BlogPost::class, mappedBy: 'tags')]
    private Collection $posts;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->posts = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, BlogPost>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
}
