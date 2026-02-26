<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

readonly class ConversationEnsureForAcceptedApplicationMessage implements MessageHighInterface
{
    public function __construct(
        public string $applicationId,
    ) {
    }
}
