<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils\Interfaces;

use Exception;

/**
 * @package App\Tool\Application\Service\Utils\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface WaitElasticServiceInterface
{
    /**
     * Get elastic info and check connection. Throwing an exception if connection is not possible.
     *
     * @throws Exception
     *
     * @return callable|array<int|string, mixed>
     */
    public function getInfo(): mixed;
}
