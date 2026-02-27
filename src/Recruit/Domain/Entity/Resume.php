<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Resume.
 *
 * @package App\Recruit\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
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

    #[ORM\Column(name: 'is_public', type: Types::BOOLEAN, options: [
        'default' => false,
    ])]
    #[Groups(['Resume', 'Resume.isPublic', 'Resume.create', 'Resume.show', 'Resume.edit'])]
    private bool $isPublic = false;

    /**
     * @var Collection<int, ResumeExperience>
     */
    #[ORM\OneToMany(targetEntity: ResumeExperience::class, mappedBy: 'resume', orphanRemoval: true)]
    private Collection $resumeExperiences;

    /**
     * @var Collection<int, ResumeEducation>
     */
    #[ORM\OneToMany(targetEntity: ResumeEducation::class, mappedBy: 'resume', orphanRemoval: true)]
    private Collection $resumeEducation;

    /**
     * @var Collection<int, ResumeSkill>
     */
    #[ORM\OneToMany(targetEntity: ResumeSkill::class, mappedBy: 'resume', orphanRemoval: true)]
    private Collection $resumeSkills;

    /**
     * @var Collection<int, ResumeReference>
     */
    #[ORM\OneToMany(targetEntity: ResumeReference::class, mappedBy: 'resume', orphanRemoval: true)]
    private Collection $resumeReferences;

    /**
     * @var Collection<int, ResumeProject>
     */
    #[ORM\OneToMany(targetEntity: ResumeProject::class, mappedBy: 'resume', orphanRemoval: true)]
    private Collection $resumeProjects;

    /**
     * @var Collection<int, ResumeLanguage>
     */
    #[ORM\OneToMany(targetEntity: ResumeLanguage::class, mappedBy: 'resume', orphanRemoval: true)]
    private Collection $languages;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->resumeExperiences = new ArrayCollection();
        $this->resumeEducation = new ArrayCollection();
        $this->resumeSkills = new ArrayCollection();
        $this->resumeReferences = new ArrayCollection();
        $this->resumeProjects = new ArrayCollection();
        $this->languages = new ArrayCollection();
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

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    /**
     * @return Collection<int, ResumeExperience>
     */
    public function getResumeExperiences(): Collection
    {
        return $this->resumeExperiences;
    }

    /**
     * @return Collection<int, ResumeEducation>
     */
    public function getResumeEducation(): Collection
    {
        return $this->resumeEducation;
    }

    /**
     * @return Collection<int, ResumeSkill>
     */
    public function getResumeSkills(): Collection
    {
        return $this->resumeSkills;
    }

    /**
     * @return Collection<int, ResumeReference>
     */
    public function getResumeReferences(): Collection
    {
        return $this->resumeReferences;
    }

    /**
     * @return Collection<int, ResumeProject>
     */
    public function getResumeProjects(): Collection
    {
        return $this->resumeProjects;
    }

    /**
     * @return Collection<int, ResumeLanguage>
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }
}
