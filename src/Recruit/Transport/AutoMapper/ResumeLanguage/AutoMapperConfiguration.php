<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeLanguage;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\ResumeLanguage\ResumeLanguageCreate;
use App\Recruit\Application\DTO\ResumeLanguage\ResumeLanguagePatch;
use App\Recruit\Application\DTO\ResumeLanguage\ResumeLanguageUpdate;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ResumeLanguageCreate::class,
        ResumeLanguageUpdate::class,
        ResumeLanguagePatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
