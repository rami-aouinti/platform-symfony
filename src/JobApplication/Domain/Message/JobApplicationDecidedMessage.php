<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

/**
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

readonly class JobApplicationDecidedMessage implements MessageHighInterface
{
    public function __construct(
        public string $applicationId,
        public string $candidateUserId,
        public string $offerId,
        public string $status,
    ) {
    }
}

