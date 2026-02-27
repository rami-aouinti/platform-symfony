<?php

declare(strict_types=1);

namespace App\Blog\Domain\Entity;

use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Uuid as RamseyUuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * BlogComment.
 *
 * @package App\Blog\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'blog_comment')]
#[ORM\Index(name: 'idx_blog_comment_post_id', columns: ['post_id'])]
#[ORM\Index(name: 'idx_blog_comment_reference_type', columns: ['reference_type'])]
#[ORM\Index(name: 'idx_blog_comment_reference_id', columns: ['reference_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class BlogComment implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['BlogComment', 'BlogComment.id', 'BlogComment.show', 'BlogComment.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: BlogPost::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['BlogComment', 'BlogComment.post', 'BlogComment.create', 'BlogComment.show', 'BlogComment.edit'])]
    private ?BlogPost $post = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['BlogComment', 'BlogComment.author', 'BlogComment.show', 'BlogComment.edit'])]
    private ?User $author = null;

    #[ORM\Column(name: 'content', type: Types::TEXT, nullable: false)]
    #[Groups(['BlogComment', 'BlogComment.content', 'BlogComment.create', 'BlogComment.show', 'BlogComment.edit'])]
    private string $content = '';

    #[ORM\Column(name: 'reference_type', type: Types::STRING, length: 64, nullable: true, enumType: BlogReferenceType::class)]
    #[Groups(['BlogComment', 'BlogComment.referenceType', 'BlogComment.create', 'BlogComment.show', 'BlogComment.edit'])]
    private ?BlogReferenceType $referenceType = null;

    #[ORM\Column(name: 'reference_id', type: UuidBinaryOrderedTimeType::NAME, nullable: true)]
    #[Groups(['BlogComment', 'BlogComment.referenceId', 'BlogComment.create', 'BlogComment.show', 'BlogComment.edit'])]
    private ?UuidInterface $referenceId = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getPost(): ?BlogPost
    {
        return $this->post;
    }

    public function setPost(?BlogPost $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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

    public function getReferenceType(): ?BlogReferenceType
    {
        return $this->referenceType;
    }

    public function setReferenceType(BlogReferenceType|string|null $referenceType): self
    {
        if ($referenceType === null) {
            $this->referenceType = null;

            return $this;
        }

        $this->referenceType = $referenceType instanceof BlogReferenceType ? $referenceType : BlogReferenceType::from($referenceType);

        return $this;
    }

    public function getReferenceId(): ?string
    {
        return $this->referenceId?->toString();
    }

    public function setReferenceId(UuidInterface|string|null $referenceId): self
    {
        if ($referenceId === null) {
            $this->referenceId = null;

            return $this;
        }

        $this->referenceId = $referenceId instanceof UuidInterface ? $referenceId : RamseyUuid::fromString($referenceId);

        return $this;
    }
}
