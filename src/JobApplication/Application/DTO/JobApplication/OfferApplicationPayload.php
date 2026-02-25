<?php

declare(strict_types=1);

namespace App\JobApplication\Application\DTO\JobApplication;

final class OfferApplicationPayload
{
    /**
     * @param string[]|null $attachments
     */
    public function __construct(
        private readonly ?string $coverLetter,
        private readonly ?string $cvUrl,
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

    /**
     * @return string[]|null
     */
    public function getAttachments(): ?array
    {
        return $this->attachments;
    }
}

