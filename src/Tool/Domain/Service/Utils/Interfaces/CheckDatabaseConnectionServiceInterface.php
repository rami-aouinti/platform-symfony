<?php

declare(strict_types=1);

namespace App\Tool\Domain\Service\Utils\Interfaces;

use Exception;

/**
 * @package App\Tool
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface CheckDatabaseConnectionServiceInterface
{
    /**
     * Check if database connection is possible. Throwing an exception if connection is not possible.
     *
     * @throws Exception
     */
    public function checkConnection(): bool;
}
