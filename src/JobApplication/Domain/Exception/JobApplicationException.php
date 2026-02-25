<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Exception;

use App\General\Application\Exception\Interfaces\ClientErrorInterface;
use RuntimeException;

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
