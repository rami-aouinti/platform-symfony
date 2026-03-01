<?php

declare(strict_types=1);

namespace App\Configuration\Domain\Repository\Interfaces;

use App\Configuration\Domain\Entity\Configuration;
use App\User\Domain\Entity\UserProfile;

/**
 * ConfigurationRepositoryInterface.
 *
 * @package App\Configuration\Domain\Repository\Interfaces
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
interface ConfigurationRepositoryInterface
{
    /**
     * @return Configuration[]
     */
    public function findByProfileAndKeyName(UserProfile $profile, ?string $keyName = null): array;
}
