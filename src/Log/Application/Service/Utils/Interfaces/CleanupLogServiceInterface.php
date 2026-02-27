<?php

declare(strict_types=1);

namespace App\Log\Application\Service\Utils\Interfaces;

use Throwable;

/**
 * @package App\Log\Application\Service\Utils\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface CleanupLogServiceInterface
{
    /**
     * Cleanup db tables with logs
     *
     * @throws Throwable
     */
    public function cleanup(): bool;
}
