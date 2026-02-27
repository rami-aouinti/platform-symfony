<?php

declare(strict_types=1);

namespace App\Blog\Domain\Entity;

use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * BlogPostLink.
 *
 * @package App\Blog\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'blog_post_link')]
#[ORM\Index(name: 'idx_blog_post_link_post_id', columns: ['post_id'])]
#[ORM\Index(name: 'idx_blog_post_link_task_id', columns: ['task_id'])]
#[ORM\Index(name: 'idx_blog_post_link_task_request_id', columns: ['task_request_id'])]
#[ORM\UniqueConstraint(name: 'uq_blog_post_link_post_task', columns: ['post_id', 'task_id'])]
#[ORM\UniqueConstraint(name: 'uq_blog_post_link_post_request', columns: ['post_id', 'task_request_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class BlogPostLink implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['BlogPostLink', 'BlogPostLink.id', 'BlogPostLink.show', 'BlogPostLink.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: BlogPost::class)]
    #[ORM\JoinColumn(name: 'post_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['BlogPostLink', 'BlogPostLink.post', 'BlogPostLink.create', 'BlogPostLink.show', 'BlogPostLink.edit'])]
    private ?BlogPost $post = null;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    #[Groups(['BlogPostLink', 'BlogPostLink.task', 'BlogPostLink.create', 'BlogPostLink.show', 'BlogPostLink.edit'])]
    private ?Task $task = null;

    #[ORM\ManyToOne(targetEntity: TaskRequest::class)]
    #[ORM\JoinColumn(name: 'task_request_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    #[Groups(['BlogPostLink', 'BlogPostLink.taskRequest', 'BlogPostLink.create', 'BlogPostLink.show', 'BlogPostLink.edit'])]
    private ?TaskRequest $taskRequest = null;

    #[ORM\Column(name: 'reference_type', type: Types::STRING, length: 64, nullable: false, enumType: BlogReferenceType::class)]
    #[Groups(['BlogPostLink', 'BlogPostLink.referenceType', 'BlogPostLink.create', 'BlogPostLink.show', 'BlogPostLink.edit'])]
    private BlogReferenceType $referenceType = BlogReferenceType::TASK;

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

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getTaskRequest(): ?TaskRequest
    {
        return $this->taskRequest;
    }

    public function setTaskRequest(?TaskRequest $taskRequest): self
    {
        $this->taskRequest = $taskRequest;

        return $this;
    }

    public function getReferenceType(): BlogReferenceType
    {
        return $this->referenceType;
    }

    public function setReferenceType(BlogReferenceType|string $referenceType): self
    {
        $this->referenceType = $referenceType instanceof BlogReferenceType ? $referenceType : BlogReferenceType::from($referenceType);

        return $this;
    }
}
