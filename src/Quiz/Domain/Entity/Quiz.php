<?php

declare(strict_types=1);

namespace App\Quiz\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'quiz')]
#[ORM\Index(name: 'idx_quiz_owner_id', columns: ['owner_id'])]
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
}
