<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\JobApplication;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Recruit\Application\Resource\JobOfferResource;
use App\Recruit\Application\Resource\ResumeResource;
use App\Recruit\Domain\Entity\JobOffer;
use App\Recruit\Domain\Entity\Resume;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'jobOffer', 'candidate', 'coverLetter', 'cvUrl', 'resume', 'attachments', 'status', 'decidedBy', 'decidedAt',
    ];

    public function __construct(
        private readonly JobOfferResource $jobOfferResource,
        private readonly ResumeResource $resumeResource,
        private readonly UserResource $userResource,
    ) {
    }

    protected function transformJobOffer(?string $jobOffer): ?JobOffer
    {
        try {
            return $jobOffer !== null && $jobOffer !== '' ? $this->jobOfferResource->getReference($jobOffer) : null;
        } catch (Throwable) {
            return null;
        }
    }
    protected function transformResume(?string $resume): ?Resume
    {
        try {
            return $resume !== null && $resume !== '' ? $this->resumeResource->getReference($resume) : null;
        } catch (Throwable) {
            return null;
        }
    }
    protected function transformCandidate(?string $candidate): ?User
    {
        try {
            return $candidate !== null && $candidate !== '' ? $this->userResource->getReference($candidate) : null;
        } catch (Throwable) {
            return null;
        }
    }
    protected function transformDecidedBy(?string $decidedBy): ?User
    {
        try {
            return $decidedBy !== null && $decidedBy !== '' ? $this->userResource->getReference($decidedBy) : null;
        } catch (Throwable) {
            return null;
        }
    }
    protected function transformDecidedAt(?string $decidedAt): ?DateTimeImmutable
    {
        return $decidedAt !== null && $decidedAt !== '' ? new DateTimeImmutable($decidedAt) : null;
    }
}
