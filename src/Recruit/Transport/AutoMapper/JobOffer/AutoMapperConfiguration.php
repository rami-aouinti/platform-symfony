<?php

declare(strict_types=1);

namespace App\Recruit\Transport\AutoMapper\JobOffer;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Recruit\Application\DTO\JobOffer\JobOfferCreate;
use App\Recruit\Application\DTO\JobOffer\JobOfferPatch;
use App\Recruit\Application\DTO\JobOffer\JobOfferUpdate;

/**
 * AutoMapperConfiguration.
 *
 * @package App\Recruit\Transport\AutoMapper\JobOffer
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    protected static array $requestMapperClasses = [
        JobOfferCreate::class,
        JobOfferUpdate::class,
        JobOfferPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
