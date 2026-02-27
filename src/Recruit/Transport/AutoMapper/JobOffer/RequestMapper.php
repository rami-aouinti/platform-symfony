<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\JobOffer;

use App\Company\Application\Resource\CompanyResource;
use App\Company\Domain\Entity\Company;
use App\General\Application\DTO\Address;
use App\General\Transport\AutoMapper\RestRequestMapper;
use DateTimeImmutable;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'title', 'description', 'location', 'employmentType', 'status', 'salaryMin', 'salaryMax', 'salaryCurrency', 'salaryPeriod',
        'remoteMode', 'experienceLevel', 'workTime', 'applicationType', 'publishedAt', 'address', 'languageLevel', 'company',
    ];

    public function __construct(
        private readonly CompanyResource $companyResource
    ) {
    }

    protected function transformPublishedAt(?string $publishedAt): ?DateTimeImmutable
    {
        return $publishedAt !== null && $publishedAt !== '' ? new DateTimeImmutable($publishedAt) : null;
    }

    protected function transformAddress(?array $address): ?Address
    {
        if (!is_array($address)) {
            return null;
        }

        return (new Address())
            ->setStreetLine1(isset($address['streetLine1']) ? (string)$address['streetLine1'] : null)
            ->setStreetLine2(isset($address['streetLine2']) ? (string)$address['streetLine2'] : null)
            ->setPostalCode(isset($address['postalCode']) ? (string)$address['postalCode'] : null)
            ->setCity((string)($address['city'] ?? ''))
            ->setRegion(isset($address['region']) ? (string)$address['region'] : null)
            ->setCountryCode((string)($address['countryCode'] ?? ''));
    }

    protected function transformCompany(?string $company): ?Company
    {
        try {
            return $company !== null && $company !== '' ? $this->companyResource->getReference($company) : null;
        } catch (Throwable) {
            return null;
        }
    }
}
