<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeExperience;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Recruit\Application\Resource\ResumeResource;
use App\Recruit\Domain\Entity\Resume;
use DateTimeImmutable;
use Throwable;

/**
 * RequestMapper.
 *
 * @package App\Recruit\Transport\AutoMapper\ResumeExperience
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'resume',
        'title',
        'companyName',
        'employmentType',
        'startDate',
        'endDate',
        'isCurrent',
        'location',
        'description',
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

    protected function transformStartDate(?string $startDate): ?DateTimeImmutable
    {
        return $startDate !== null && $startDate !== '' ? new DateTimeImmutable($startDate) : null;
    }

    protected function transformEndDate(?string $endDate): ?DateTimeImmutable
    {
        return $endDate !== null && $endDate !== '' ? new DateTimeImmutable($endDate) : null;
    }
}
