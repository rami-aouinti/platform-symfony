<?php

declare(strict_types=1);

namespace App\Tool\Application\Service\Utils\Interfaces;

use Exception;

/**
 * @package App\Tool\Application\Service\Utils\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface MessengerMessagesServiceInterface
{
    /**
     * @throws Exception
     */
    public function cleanUp(): int;
}
