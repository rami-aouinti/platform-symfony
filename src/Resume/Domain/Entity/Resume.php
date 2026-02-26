<?php

declare(strict_types=1);

namespace App\Resume\Domain\Entity;

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
#[ORM\Table(name: 'resume')]
#[ORM\Index(name: 'idx_resume_owner_created_at', columns: ['owner_id', 'created_at'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Resume implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Resume', 'Resume.id', 'Resume.show', 'Resume.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['Resume', 'Resume.owner', 'Resume.show'])]
    private ?User $owner = null;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Resume', 'Resume.title', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private string $title = '';

    #[ORM\Column(name: 'summary', type: Types::TEXT, nullable: false)]
    #[Groups(['Resume', 'Resume.summary', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private string $summary = '';

    /**
     * @var array<int, array<string, mixed>>
     */
    #[ORM\Column(name: 'experiences', type: Types::JSON, nullable: false)]
    #[Groups(['Resume', 'Resume.experiences', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private array $experiences = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    #[ORM\Column(name: 'education', type: Types::JSON, nullable: false)]
    #[Groups(['Resume', 'Resume.education', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private array $education = [];

    /**
     * @var array<int, string>
     */
    #[ORM\Column(name: 'skills', type: Types::JSON, nullable: false)]
    #[Groups(['Resume', 'Resume.skills', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private array $skills = [];

    /**
     * @var array<int, array<string, string>>
     */
    #[ORM\Column(name: 'links', type: Types::JSON, nullable: false)]
    #[Groups(['Resume', 'Resume.links', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private array $links = [];

    #[ORM\Column(name: 'is_public', type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['Resume', 'Resume.isPublic', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private bool $isPublic = false;

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

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getExperiences(): array
    {
        return $this->experiences;
    }

    /**
     * @param array<int, array<string, mixed>> $experiences
     */
    public function setExperiences(array $experiences): self
    {
        $this->experiences = $experiences;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getEducation(): array
    {
        return $this->education;
    }

    /**
     * @param array<int, array<string, mixed>> $education
     */
    public function setEducation(array $education): self
    {
        $this->education = $education;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getSkills(): array
    {
        return $this->skills;
    }

    /**
     * @param array<int, string> $skills
     */
    public function setSkills(array $skills): self
    {
        $this->skills = $skills;

        return $this;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array<int, array<string, string>> $links
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }
}
