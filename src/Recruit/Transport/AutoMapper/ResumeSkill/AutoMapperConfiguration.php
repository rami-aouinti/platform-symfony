<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeSkill;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\ResumeSkill\ResumeSkillCreate;
use App\Recruit\Application\DTO\ResumeSkill\ResumeSkillPatch;
use App\Recruit\Application\DTO\ResumeSkill\ResumeSkillUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Recruit\Transport\AutoMapper\ResumeSkill
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ResumeSkillCreate::class,
        ResumeSkillUpdate::class,
        ResumeSkillPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
