<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeSkill;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Recruit\Application\Resource\ResumeResource;
use App\Recruit\Domain\Entity\Resume;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'resume',
        'name',
        'level',
        'yearsExperience',
        'sortOrder',
    ];

    public function __construct(
        private readonly ResumeResource $resumeResource
    ) {
    }

    protected function transformResume(?string $resume): ?Resume
    {
        if ($resume === null || $resume === '') {
            return null;
        }

        try {
            return $this->resumeResource->getReference($resume);
        } catch (Throwable) {
            return null;
        }
    }
}
