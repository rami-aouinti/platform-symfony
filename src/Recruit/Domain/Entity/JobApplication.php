<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\Chat\Domain\Entity\Conversation;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\Recruit\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'job_application')]
#[ORM\UniqueConstraint(name: 'uq_job_application_job_offer_candidate', columns: ['job_offer_id', 'candidate_id'])]
#[ORM\Index(name: 'idx_job_application_job_offer_status', columns: ['job_offer_id', 'status'])]
#[ORM\Index(name: 'idx_job_application_candidate_status', columns: ['candidate_id', 'status'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class JobApplication implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['JobApplication', 'JobApplication.id', 'JobApplication.show', 'JobOffer'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: JobOffer::class, inversedBy: 'jobApplications')]
    #[ORM\JoinColumn(name: 'job_offer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobApplication', 'JobApplication.jobOffer', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit'])]
    private ?JobOffer $jobOffer = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'candidate_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['JobApplication', 'JobApplication.candidate', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit', 'JobOffer'])]
    private ?User $candidate = null;

    #[ORM\Column(name: 'cover_letter', type: Types::TEXT, nullable: true)]
    #[Groups(['JobApplication', 'JobApplication.coverLetter', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit'])]
    private ?string $coverLetter = null;

    #[ORM\Column(name: 'cv_url', type: Types::STRING, length: 2048, nullable: true)]
    #[Groups(['JobApplication', 'JobApplication.cvUrl', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit', 'JobOffer'])]
    private ?string $cvUrl = null;

    #[ORM\ManyToOne(targetEntity: Resume::class)]
    #[ORM\JoinColumn(name: 'resume_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['JobApplication', 'JobApplication.resume', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit', 'JobOffer'])]
    private ?Resume $resume = null;

    #[ORM\Column(name: 'attachments', type: Types::JSON, nullable: true)]
    #[Groups(['JobApplication', 'JobApplication.attachments', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit', 'JobOffer'])]
    private ?array $attachments = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 32, nullable: false, enumType: JobApplicationStatus::class)]
    #[Groups(['JobApplication', 'JobApplication.status', 'JobApplication.create', 'JobApplication.show', 'JobApplication.edit', 'JobOffer'])]
    private JobApplicationStatus $status = JobApplicationStatus::PENDING;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'decided_by_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['JobApplication.decidedBy', 'JobApplication.show', 'JobOffer'])]
    private ?User $decidedBy = null;

    #[ORM\Column(name: 'decided_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['JobApplication.decidedAt', 'JobApplication.show', 'JobOffer'])]
    private ?DateTimeImmutable $decidedAt = null;

    #[ORM\OneToOne(targetEntity: Conversation::class, mappedBy: 'jobApplication')]
    private ?Conversation $conversation = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getJobOffer(): ?JobOffer
    {
        return $this->jobOffer;
    }

    public function setJobOffer(?JobOffer $jobOffer): self
    {
        $this->jobOffer = $jobOffer;

        return $this;
    }

    public function getCandidate(): ?User
    {
        return $this->candidate;
    }

    public function setCandidate(?User $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    public function getCoverLetter(): ?string
    {
        return $this->coverLetter;
    }

    public function setCoverLetter(?string $coverLetter): self
    {
        $this->coverLetter = $coverLetter;

        return $this;
    }

    public function getCvUrl(): ?string
    {
        return $this->cvUrl;
    }

    public function setCvUrl(?string $cvUrl): self
    {
        $this->cvUrl = $cvUrl;

        return $this;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * @return array<mixed>|null
     */
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }

    /**
     * @param array<mixed>|null $attachments
     */
    public function setAttachments(?array $attachments): self
    {
        $this->attachments = $attachments;

        return $this;
    }

    public function getStatus(): JobApplicationStatus
    {
        return $this->status;
    }

    public function setStatus(JobApplicationStatus|string $status): self
    {
        $this->status = $status instanceof JobApplicationStatus ? $status : JobApplicationStatus::from($status);

        return $this;
    }

    public function getDecidedBy(): ?User
    {
        return $this->decidedBy;
    }

    public function setDecidedBy(?User $decidedBy): self
    {
        $this->decidedBy = $decidedBy;

        return $this;
    }

    public function getDecidedAt(): ?DateTimeImmutable
    {
        return $this->decidedAt;
    }

    public function setDecidedAt(?DateTimeImmutable $decidedAt): self
    {
        $this->decidedAt = $decidedAt;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;

        return $this;
    }
}
