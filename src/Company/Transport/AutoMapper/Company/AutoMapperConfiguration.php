<?php

declare(strict_types=1);

namespace App\Company\Transport\AutoMapper\Company;

use App\Company\Application\DTO\Company\CompanyCreate;
use App\Company\Application\DTO\Company\CompanyPatch;
use App\Company\Application\DTO\Company\CompanyUpdate;
use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Company\Transport\AutoMapper\Company
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        CompanyCreate::class,
        CompanyUpdate::class,
        CompanyPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
