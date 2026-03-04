<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\DTO;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class UserApplicationConfigurationCreatePayload
{
    /**
     * @param array<mixed> $value
     */
    public function __construct(
        private string $code,
        private string $keyName,
        private array $value,
        private string $status,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getKeyName(): string
    {
        return $this->keyName;
    }

    /**
     * @return array<mixed>
     */
    public function getValue(): array
    {
        return $this->value;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param mixed $payload
     */
    public static function fromPayload(mixed $payload): self
    {
        if (!is_array($payload)) {
            throw new BadRequestHttpException('Payload must be an object.');
        }

        $code = $payload['code'] ?? null;
        $keyName = $payload['keyName'] ?? null;
        $value = $payload['value'] ?? null;
        $status = $payload['status'] ?? 'active';

        if (!is_string($code) || trim($code) === '') {
            throw new BadRequestHttpException('"code" must be a non-empty string.');
        }

        if (!is_string($keyName) || trim($keyName) === '') {
            throw new BadRequestHttpException('"keyName" must be a non-empty string.');
        }

        if (!is_array($value)) {
            throw new BadRequestHttpException('"value" must be an array.');
        }

        if (!is_string($status) || trim($status) === '') {
            throw new BadRequestHttpException('"status" must be a non-empty string.');
        }

        return new self(trim($code), trim($keyName), $value, trim($status));
    }
}
