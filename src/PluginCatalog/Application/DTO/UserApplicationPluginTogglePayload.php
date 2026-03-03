<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\DTO;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

use function array_key_exists;
use function is_array;
use function is_bool;

final readonly class UserApplicationPluginTogglePayload
{
    public function __construct(
        private bool $active,
    ) {
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param mixed $payload
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!is_array($payload) || !array_key_exists('active', $payload) || !is_bool($payload['active'])) {
            throw new BadRequestHttpException('Payload must contain a boolean "active" property.');
        }

        return new self($payload['active']);
    }
}
