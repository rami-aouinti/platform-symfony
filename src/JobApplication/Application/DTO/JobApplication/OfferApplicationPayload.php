<?php

declare(strict_types=1);

namespace App\JobApplication\Application\DTO\JobApplication;

/**
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class OfferApplicationPayload
{
    /**
     * @param string[]|null $attachments
     */
    public function __construct(
        private readonly ?string $coverLetter,
        private readonly ?string $cvUrl,
        private readonly ?string $resumeId,
        private readonly ?array $attachments,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(array $payload): self
    {
        $attachments = $payload['attachments'] ?? null;

        return new self(
            isset($payload['coverLetter']) ? (string) $payload['coverLetter'] : null,
            isset($payload['cvUrl']) ? (string) $payload['cvUrl'] : null,
            isset($payload['resumeId']) ? (string) $payload['resumeId'] : null,
            is_array($attachments) ? array_values(array_map(static fn (mixed $item): string => (string) $item, $attachments)) : null,
        );
    }

    public function getCoverLetter(): ?string
    {
        return $this->coverLetter;
    }

    public function getCvUrl(): ?string
    {
        return $this->cvUrl;
    }

    public function getResumeId(): ?string
    {
        return $this->resumeId;
    }

    /**
     * @return string[]|null
     */
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }
}

