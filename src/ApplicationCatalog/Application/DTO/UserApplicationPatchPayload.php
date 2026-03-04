<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class UserApplicationPatchPayload
{
    public function __construct(
        private ?string $name,
        private ?string $logo,
        private ?string $description,
        private ?bool $public,
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    /**
     * @param mixed $payload
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!is_array($payload)) {
            throw new BadRequestHttpException('Payload must be an object.');
        }

        $name = $payload['name'] ?? null;
        if ($name !== null && !is_string($name)) {
            throw new BadRequestHttpException('"name" must be a string or null.');
        }

        $logo = $payload['logo'] ?? null;
        if ($logo !== null && !is_string($logo)) {
            throw new BadRequestHttpException('"logo" must be a string or null.');
        }

        $description = $payload['description'] ?? null;
        if ($description !== null && !is_string($description)) {
            throw new BadRequestHttpException('"description" must be a string or null.');
        }

        $public = $payload['public'] ?? null;
        if ($public !== null && !is_bool($public)) {
            throw new BadRequestHttpException('"public" must be a boolean or null.');
        }

        return new self($name, $logo, $description, $public);
    }
}
