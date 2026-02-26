<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\JobApplication;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\JobApplication\JobApplicationCreate;
use App\Recruit\Application\DTO\JobApplication\JobApplicationPatch;
use App\Recruit\Application\DTO\JobApplication\JobApplicationUpdate;

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        JobApplicationCreate::class,
        JobApplicationUpdate::class,
        JobApplicationPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
