<?php

declare(strict_types=1);

namespace App\Company\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

/**
 * CompanyStatus.
 *
 * @package App\Company\Domain\Enum
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
enum CompanyStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';

    public function canTransitionTo(self $target): bool
    {
        return $this !== $target;
    }
}
