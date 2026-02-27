<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\JobApplication;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\JobApplication\JobApplicationApply;
use App\Recruit\Application\DTO\JobApplication\JobApplicationUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Recruit\Transport\AutoMapper\JobApplication
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        JobApplicationApply::class,
        JobApplicationUpdate::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
