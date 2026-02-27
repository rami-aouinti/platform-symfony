<?php

declare(strict_types=1);

namespace App\Tool\Domain\Repository\Interfaces;

use Exception;

/**
 * @package App\Tool\Domain\Repository\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface MessengerMessagesRepositoryInterface
{
    /**
     * @throws Exception
     */
    public function cleanUp(): int;
}
