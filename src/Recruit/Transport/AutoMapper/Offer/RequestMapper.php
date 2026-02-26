<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\Offer;

use App\Company\Application\Resource\CompanyResource;
use App\Company\Domain\Entity\Company;
use App\General\Transport\AutoMapper\RestRequestMapper;
use Throwable;

/**
 * @package App\Offer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class RequestMapper extends RestRequestMapper
{
    /**
     * @var array<int, non-empty-string>
     */
    protected static array $properties = [
        'title',
        'description',
        'status',
        'company',
    ];

    public function __construct(
        private readonly CompanyResource $companyResource,
    ) {
    }

    /**
     * @throws Throwable
     */
    protected function transformCompany(?string $company): ?Company
    {
        return $company !== null ? $this->companyResource->getReference($company) : null;
    }
}
