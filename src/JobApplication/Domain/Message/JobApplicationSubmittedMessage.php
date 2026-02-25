<?php

declare(strict_types=1);

namespace App\JobApplication\Domain\Message;

use App\General\Domain\Message\Interfaces\MessageHighInterface;

readonly class JobApplicationSubmittedMessage implements MessageHighInterface
{
    public function __construct(
        public string $jobApplicationId,
        public string $jobOfferId,
        public string $jobOfferTitle,
        public string $candidateId,
        public string $candidateEmail,
        public ?string $reviewerId,
        public ?string $reviewerEmail,
    ) {
    }
}
