<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

/**
 * @package App\Recruit\Domain\Message
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

readonly class ConversationEnsureForAcceptedApplicationMessage implements MessageHighInterface
{
    public function __construct(
        public string $applicationId,
    ) {
    }
}
