<?php

declare(strict_types=1);

namespace App\Company\Transport\AutoMapper\Company;

use App\General\Application\DTO\Address;
use App\General\Transport\AutoMapper\RestRequestMapper;

/**
 * RequestMapper.
 *
 * @package App\Company\Transport\AutoMapper\Company
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'legalName',
        'slug',
        'status',
        'mainAddress',
    ];

    protected function transformMainAddress(?array $mainAddress): ?Address
    {
        if (!is_array($mainAddress)) {
            return null;
        }

        return (new Address())
            ->setStreetLine1(isset($mainAddress['streetLine1']) ? (string)$mainAddress['streetLine1'] : null)
            ->setStreetLine2(isset($mainAddress['streetLine2']) ? (string)$mainAddress['streetLine2'] : null)
            ->setPostalCode(isset($mainAddress['postalCode']) ? (string)$mainAddress['postalCode'] : null)
            ->setCity((string)($mainAddress['city'] ?? ''))
            ->setRegion(isset($mainAddress['region']) ? (string)$mainAddress['region'] : null)
            ->setCountryCode((string)($mainAddress['countryCode'] ?? ''));
    }
}
