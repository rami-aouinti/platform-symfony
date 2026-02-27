<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeEducation;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\ResumeEducation\ResumeEducationCreate;
use App\Recruit\Application\DTO\ResumeEducation\ResumeEducationPatch;
use App\Recruit\Application\DTO\ResumeEducation\ResumeEducationUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Recruit\Transport\AutoMapper\ResumeEducation
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ResumeEducationCreate::class,
        ResumeEducationUpdate::class,
        ResumeEducationPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
