<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeProject;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\ResumeProject\ResumeProjectCreate;
use App\Recruit\Application\DTO\ResumeProject\ResumeProjectPatch;
use App\Recruit\Application\DTO\ResumeProject\ResumeProjectUpdate;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ResumeProjectCreate::class,
        ResumeProjectUpdate::class,
        ResumeProjectPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
