<?php

declare(strict_types=1);

namespace App\Company\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageLowInterface;

readonly class CompanyCreatedMessage implements MessageLowInterface
{
    public function __construct(
        public string $companyId,
        public string $companyLegalName,
        public string $ownerId,
        public string $ownerEmail,
    ) {
    }
}
