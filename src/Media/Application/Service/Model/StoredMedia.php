<?php

declare(strict_types=1);

namespace App\Media\Application\Service\Model;

/**
 * @package App\Media
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final readonly class StoredMedia
{
    public function __construct(
        private string $originalName,
        private string $path,
        private string $mimeType,
        private int $size,
    ) {
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
