<?php

declare(strict_types=1);

namespace App\User\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * @package App\User
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

enum AddressType: string implements DatabaseEnumInterface
{
    use GetValues;

    case BILLING = 'billing';
    case SHIPPING = 'shipping';
    case HOME = 'home';
    case COMPANY_HQ = 'company_hq';
    case OTHER = 'other';
}
