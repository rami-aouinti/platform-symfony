<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeExperience;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\ResumeExperience\ResumeExperienceCreate;
use App\Recruit\Application\DTO\ResumeExperience\ResumeExperiencePatch;
use App\Recruit\Application\DTO\ResumeExperience\ResumeExperienceUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Recruit\Transport\AutoMapper\ResumeExperience
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ResumeExperienceCreate::class,
        ResumeExperienceUpdate::class,
        ResumeExperiencePatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
