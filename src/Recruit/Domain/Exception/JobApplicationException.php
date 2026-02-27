<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Exception;

use App\General\Application\Exception\Interfaces\ClientErrorInterface;
use RuntimeException;

/**
 * @package App\Recruit\Domain\Exception
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class JobApplicationException extends RuntimeException implements ClientErrorInterface
{
    public function __construct(string $message, int $statusCode)
    {
        parent::__construct($message, $statusCode);
    }

    public function getStatusCode(): int
    {
        return $this->getCode();
    }
}
