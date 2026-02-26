<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeEducation;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Recruit\Application\Resource\ResumeResource;
use App\Recruit\Domain\Entity\Resume;
use DateTimeImmutable;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'resume',
        'schoolName',
        'degree',
        'fieldOfStudy',
        'level',
        'startDate',
        'endDate',
        'isCurrent',
        'description',
        'sortOrder',
    ];

    public function __construct(private readonly ResumeResource $resumeResource)
    {
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

    protected function transformStartDate(?string $startDate): ?DateTimeImmutable
    {
        return $startDate !== null && $startDate !== '' ? new DateTimeImmutable($startDate) : null;
    }

    protected function transformEndDate(?string $endDate): ?DateTimeImmutable
    {
        return $endDate !== null && $endDate !== '' ? new DateTimeImmutable($endDate) : null;
    }
}
