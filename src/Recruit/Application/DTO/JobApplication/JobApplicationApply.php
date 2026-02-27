<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\JobApplication;

use App\General\Application\DTO\RestDto;
use App\General\Application\Validator\Constraints as AppAssert;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\JobApplication as Entity;
use App\Recruit\Domain\Entity\JobOffer;
use App\Recruit\Domain\Entity\Resume;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

class JobApplicationApply extends RestDto
{
    #[Assert\NotNull]
    #[AppAssert\EntityReferenceExists(JobOffer::class)]
    protected ?JobOffer $jobOffer = null;

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

    public function setCvUrl(?string $cvUrl): self
    {
        $this->setVisited('cvUrl');
        $this->cvUrl = $cvUrl;

        return $this;
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

    /**
     * @param EntityInterface|Entity $entity
     */
    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->jobOffer = $entity->getJobOffer();
            $this->coverLetter = $entity->getCoverLetter();
            $this->cvUrl = $entity->getCvUrl();
            $this->resume = $entity->getResume();
            $this->attachments = $entity->getAttachments();
        }

        return $this;
    }
}
