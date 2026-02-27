<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum JobOfferStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case DRAFT = 'draft';
    case OPEN = 'open';
    case CLOSED = 'closed';
}
