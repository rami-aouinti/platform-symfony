<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\JobApplication;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\JobApplication as Entity;
use App\Recruit\Domain\Entity\JobOffer;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method self|RestDtoInterface get(string $id)
 * @method self|RestDtoInterface patch(RestDtoInterface $dto)
 * @method Entity|EntityInterface update(EntityInterface $entity)
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class JobApplication extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(JobOffer::class)]
    protected ?JobOffer $jobOffer = null;

    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(User::class)]
    protected ?User $candidate = null;

    protected ?string $coverLetter = null;

    #[Assert\Length(max: 2048)]
    #[Assert\Url]
    protected ?string $cvUrl = null;

    #[AppAssert\EntityReferenceExists(Resume::class)]
    protected ?Resume $resume = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $attachments = null;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [JobApplicationStatus::class, 'getValues'])]
    protected string $status = JobApplicationStatus::PENDING->value;

    #[AppAssert\EntityReferenceExists(User::class)]
    protected ?User $decidedBy = null;

    protected ?DateTimeImmutable $decidedAt = null;

    public function getJobOffer(): ?JobOffer
    {
        return $this->jobOffer;
    }

    public function setJobOffer(?JobOffer $jobOffer): self
    {
        $this->setVisited('jobOffer');
        $this->jobOffer = $jobOffer;

        return $this;
    }

    public function getCandidate(): ?User
    {
        return $this->candidate;
    }

    public function setCandidate(?User $candidate): self
    {
        $this->setVisited('candidate');
        $this->candidate = $candidate;

        return $this;
    }

    public function getCoverLetter(): ?string
    {
        return $this->coverLetter;
    }

    public function setCoverLetter(?string $coverLetter): self
    {
        $this->setVisited('coverLetter');
        $this->coverLetter = $coverLetter;

        return $this;
    }

    public function getCvUrl(): ?string
    {
        return $this->cvUrl;
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->setVisited('resume');
        $this->resume = $resume;

        return $this;
    }

    public function setCvUrl(?string $cvUrl): self
    {
        $this->setVisited('cvUrl');
        $this->cvUrl = $cvUrl;

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
        $this->setVisited('attachments');
        $this->attachments = $attachments;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->setVisited('status');
        $this->status = $status;

        return $this;
    }

    public function getDecidedBy(): ?User
    {
        return $this->decidedBy;
    }

    public function setDecidedBy(?User $decidedBy): self
    {
        $this->setVisited('decidedBy');
        $this->decidedBy = $decidedBy;

        return $this;
    }

    public function getDecidedAt(): ?DateTimeImmutable
    {
        return $this->decidedAt;
    }

    public function setDecidedAt(?DateTimeImmutable $decidedAt): self
    {
        $this->setVisited('decidedAt');
        $this->decidedAt = $decidedAt;

        return $this;
    }

    /**
     * @param EntityInterface|Entity $entity
     */
    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->jobOffer = $entity->getJobOffer();
            $this->candidate = $entity->getCandidate();
            $this->coverLetter = $entity->getCoverLetter();
            $this->cvUrl = $entity->getCvUrl();
            $this->resume = $entity->getResume();
            $this->attachments = $entity->getAttachments();
            $this->status = $entity->getStatus()->value;
            $this->decidedBy = $entity->getDecidedBy();
            $this->decidedAt = $entity->getDecidedAt();
        }

        return $this;
    }
}
