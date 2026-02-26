<?php

declare(strict_types=1);

namespace App\Media\Application\Service\Interfaces;

use App\Media\Application\Service\Model\StoredMedia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaStorageServiceInterface
{
    public function store(UploadedFile $file, ?string $ownerId = null): StoredMedia;

    public function delete(string $path): void;
}
