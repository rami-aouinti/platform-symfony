<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\JobApplication;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Recruit\Application\Resource\JobOfferResource;
use App\Recruit\Application\Resource\ResumeResource;
use App\Recruit\Domain\Entity\JobOffer;
use App\Recruit\Domain\Entity\Resume;
use Throwable;

/**
 * RequestMapper.
 *
 * @package App\Recruit\Transport\AutoMapper\JobApplication
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'jobOffer', 'coverLetter', 'cvUrl', 'resume', 'attachments',
    ];

    public function __construct(
        private readonly JobOfferResource $jobOfferResource,
        private readonly ResumeResource $resumeResource,
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
}
