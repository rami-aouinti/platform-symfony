<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\ResumeReference;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\ResumeReference\ResumeReferenceCreate;
use App\Recruit\Application\DTO\ResumeReference\ResumeReferencePatch;
use App\Recruit\Application\DTO\ResumeReference\ResumeReferenceUpdate;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        ResumeReferenceCreate::class,
        ResumeReferenceUpdate::class,
        ResumeReferencePatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
