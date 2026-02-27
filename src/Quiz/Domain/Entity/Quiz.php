<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Quiz.
 *
 * @package App\Quiz\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[ORM\Entity]
#[ORM\Table(name: 'quiz')]
#[ORM\Index(name: 'idx_quiz_owner_id', columns: ['owner_id'])]
#[ORM\Index(name: 'idx_quiz_category', columns: ['category'])]
#[ORM\Index(name: 'idx_quiz_difficulty', columns: ['difficulty'])]
#[ORM\Index(name: 'idx_quiz_published', columns: ['is_published'])]
#[ORM\Index(name: 'idx_quiz_starts_at', columns: ['starts_at'])]
#[ORM\Index(name: 'idx_quiz_ends_at', columns: ['ends_at'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Quiz implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Quiz', 'Quiz.id', 'Quiz.show', 'Quiz.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Quiz', 'Quiz.owner', 'Quiz.show', 'Quiz.edit'])]
    private ?User $owner = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Quiz', 'Quiz.title', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private string $title = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['Quiz', 'Quiz.description', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'category', type: Types::STRING, length: 100, nullable: false)]
    #[Groups(['Quiz', 'Quiz.category', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private string $category = 'general';

    #[ORM\Column(name: 'difficulty', type: Types::STRING, length: 20, nullable: false)]
    #[Groups(['Quiz', 'Quiz.difficulty', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private string $difficulty = 'easy';

    #[ORM\Column(name: 'time_limit', type: Types::INTEGER, nullable: true)]
    #[Groups(['Quiz', 'Quiz.timeLimit', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private ?int $timeLimit = null;

    #[ORM\Column(name: 'is_published', type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['Quiz', 'Quiz.isPublished', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private bool $isPublished = false;

    #[ORM\Column(name: 'starts_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['Quiz', 'Quiz.startsAt', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private ?DateTimeImmutable $startsAt = null;

    #[ORM\Column(name: 'ends_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['Quiz', 'Quiz.endsAt', 'Quiz.create', 'Quiz.show', 'Quiz.edit'])]
    private ?DateTimeImmutable $endsAt = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDifficulty(): string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getTimeLimit(): ?int
    {
        return $this->timeLimit;
    }

    public function setTimeLimit(?int $timeLimit): self
    {
        $this->timeLimit = $timeLimit;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getStartsAt(): ?DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(?DateTimeImmutable $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?DateTimeImmutable
    {
        return $this->endsAt;
    }

    public function setEndsAt(?DateTimeImmutable $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }
}
