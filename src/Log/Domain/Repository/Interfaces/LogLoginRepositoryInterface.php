<?php

declare(strict_types=1);

namespace App\Log\Domain\Repository\Interfaces;

use Exception;

/**
 * @package App\Log
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface LogLoginRepositoryInterface
{
    /**
     * Method to clean history data from 'log_login' table.
     *
     * @throws Exception
     */
    public function cleanHistory(): int;
}
