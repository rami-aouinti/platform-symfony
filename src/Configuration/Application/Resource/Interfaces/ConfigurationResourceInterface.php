<?php

declare(strict_types=1);

namespace App\Configuration\Application\Resource\Interfaces;

use App\Configuration\Domain\Entity\Configuration;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\ApplicationCatalog\Domain\Entity\UserApplication;

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
    public function findByUserApplicationAndKeyName(UserApplication $userApplication, ?string $keyName = null): array;
}
