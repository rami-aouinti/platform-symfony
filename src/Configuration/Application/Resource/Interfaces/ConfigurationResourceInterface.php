<?php

declare(strict_types=1);

namespace App\Configuration\Application\Resource\Interfaces;

use App\Configuration\Domain\Entity\Configuration;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\User\Domain\Entity\UserProfile;

/**
 * ConfigurationResourceInterface.
 *
 * @package App\Configuration\Application\Resource\Interfaces
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
interface ConfigurationResourceInterface extends RestResourceInterface
{
    /**
     * @return Configuration[]
     */
    public function findByProfileAndKeyName(UserProfile $profile, ?string $keyName = null): array;
}
