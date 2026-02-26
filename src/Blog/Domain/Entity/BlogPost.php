<?php

declare(strict_types=1);

namespace App\Blog\Domain\Entity;

use App\Blog\Domain\Enum\BlogPostStatus;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'blog_post')]
#[ORM\Index(name: 'idx_blog_post_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_blog_post_status', columns: ['status'])]
#[ORM\UniqueConstraint(name: 'uq_blog_post_slug', columns: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class BlogPost implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['BlogPost', 'BlogPost.id', 'BlogPost.show', 'BlogPost.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['BlogPost', 'BlogPost.owner', 'BlogPost.show', 'BlogPost.edit'])]
    private ?User $owner = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['BlogPost', 'BlogPost.title', 'BlogPost.create', 'BlogPost.show', 'BlogPost.edit'])]
    private string $title = '';

    #[ORM\Column(name: 'slug', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['BlogPost', 'BlogPost.slug', 'BlogPost.create', 'BlogPost.show', 'BlogPost.edit'])]
    private string $slug = '';

    #[ORM\Column(name: 'excerpt', type: Types::TEXT, nullable: true)]
    #[Groups(['BlogPost', 'BlogPost.excerpt', 'BlogPost.create', 'BlogPost.show', 'BlogPost.edit'])]
    private ?string $excerpt = null;

    #[ORM\Column(name: 'content', type: Types::TEXT, nullable: false)]
    #[Groups(['BlogPost', 'BlogPost.content', 'BlogPost.create', 'BlogPost.show', 'BlogPost.edit'])]
    private string $content = '';

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: BlogPostStatus::class)]
    #[Groups(['BlogPost', 'BlogPost.status', 'BlogPost.create', 'BlogPost.show', 'BlogPost.edit'])]
    private BlogPostStatus $status = BlogPostStatus::DRAFT;

    #[ORM\Column(name: 'published_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['BlogPost', 'BlogPost.publishedAt', 'BlogPost.show', 'BlogPost.edit'])]
    private ?DateTimeImmutable $publishedAt = null;

    /**
     * @var Collection<int, BlogTag>
     */
    #[ORM\ManyToMany(targetEntity: BlogTag::class, inversedBy: 'posts')]
    #[ORM\JoinTable(name: 'blog_post_tag')]
    #[Groups(['BlogPost', 'BlogPost.tags', 'BlogPost.show', 'BlogPost.edit'])]
    private Collection $tags;

    /**
     * @var Collection<int, BlogComment>
     */
    #[ORM\OneToMany(targetEntity: BlogComment::class, mappedBy: 'post', orphanRemoval: true)]
    #[Groups(['BlogPost', 'BlogPost.comments', 'BlogPost.show', 'BlogPost.edit'])]
    private Collection $comments;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): BlogPostStatus
    {
        return $this->status;
    }

    public function setStatus(BlogPostStatus|string $status): self
    {
        $nextStatus = $status instanceof BlogPostStatus ? $status : BlogPostStatus::from($status);

        if (!$this->status->canTransitionTo($nextStatus) && $this->status !== $nextStatus) {
            return $this;
        }

        $this->status = $nextStatus;

        if ($nextStatus === BlogPostStatus::PUBLISHED && $this->publishedAt === null) {
            $this->publishedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return Collection<int, BlogTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(BlogTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(BlogTag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, BlogComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
