<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Interfaces;

/**
 * @package App\Tool\Application\Service\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface VersionServiceInterface
{
    /**
     * Method to get application version from cache or create new entry to cache with version value from
     * composer.json file.
     */
    public function get(): string;
}
