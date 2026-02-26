<?php

declare(strict_types=1);

namespace App\Offer\Transport\AutoMapper\Offer;

use App\General\Transport\AutoMapper\RestAutoMapperConfiguration;
use App\Offer\Application\DTO\Offer\OfferCreate;
use App\Offer\Application\DTO\Offer\OfferPatch;
use App\Offer\Application\DTO\Offer\OfferUpdate;

/**
 * @package App\Offer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class AutoMapperConfiguration extends RestAutoMapperConfiguration
{
    /**
     * @var array<int, class-string>
     */
    protected static array $requestMapperClasses = [
        OfferCreate::class,
        OfferUpdate::class,
        OfferPatch::class,
    ];

    public function __construct(RequestMapper $requestMapper)
    {
        parent::__construct($requestMapper);
    }
}
