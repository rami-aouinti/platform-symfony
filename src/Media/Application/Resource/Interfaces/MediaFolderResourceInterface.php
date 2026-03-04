<?php

declare(strict_types=1);

namespace App\Media\Application\Resource\Interfaces;

use App\Media\Domain\Entity\MediaFolder;

interface MediaFolderResourceInterface
{
    /** @return array<int, array<string, mixed>> */
    public function list(?string $parentId, bool $tree): array;

    public function create(string $name, ?string $parentId): MediaFolder;

    public function rename(string $id, string $name): MediaFolder;

    public function delete(string $id, bool $cascade): void;

    /** @return array<string, mixed> */
    public function toArray(MediaFolder $folder): array;
}

