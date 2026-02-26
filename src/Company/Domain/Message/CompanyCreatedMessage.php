<?php

declare(strict_types=1);

namespace App\Company\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageLowInterface;

/**
 * @package App\Company
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

readonly class CompanyCreatedMessage implements MessageLowInterface
{
    /**
     * @param array<string, scalar|null> $metadata
     */
    public function __construct(
        public string $companyId,
        public string $ownerUserId,
        public array $metadata = [],
    ) {
    }
}
