<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Service;

use App\Media\Application\Service\Interfaces\MediaStorageServiceInterface;
use App\Media\Application\Service\Model\StoredMedia;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Uid\Uuid;

/**
 * @package App\Media
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final readonly class MediaStorageService implements MediaStorageServiceInterface
{
    public function __construct(
        private Filesystem $filesystem,
        private string $mediaStoragePath,
        private string $mediaStoragePublicPrefix,
    ) {
    }

    public function store(UploadedFile $file, ?string $ownerId = null): StoredMedia
    {
        $scope = $ownerId ?? 'anonymous';
        $directory = sprintf('%s/%s/%s', rtrim($this->mediaStoragePath, '/'), date('Y/m'), $scope);

        $this->filesystem->mkdir($directory);

        $extension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';
        $baseName = $this->slug((string)pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $name = sprintf('%s-%s.%s', $baseName, Uuid::v7()->toRfc4122(), strtolower($extension));

        $file->move($directory, $name);

        $relativePath = sprintf('%s/%s/%s', date('Y/m'), $scope, $name);
        $publicPath = sprintf('%s/%s', trim($this->mediaStoragePublicPrefix, '/'), $relativePath);

        return new StoredMedia(
            originalName: $file->getClientOriginalName(),
            path: '/' . ltrim($publicPath, '/'),
            mimeType: $file->getClientMimeType() ?? 'application/octet-stream',
            size: $file->getSize() ?: 0,
        );
    }

    public function delete(string $path): void
    {
        $relativePath = ltrim($path, '/');
        $prefix = trim($this->mediaStoragePublicPrefix, '/');

        if (str_starts_with($relativePath, $prefix . '/')) {
            $relativePath = substr($relativePath, strlen($prefix) + 1);
        }

        $target = sprintf('%s/%s', rtrim($this->mediaStoragePath, '/'), $relativePath);

        if ($this->filesystem->exists($target)) {
            $this->filesystem->remove($target);
        }
    }

    private function slug(string $name): string
    {
        $slugger = new AsciiSlugger();
        $slug = strtolower($slugger->slug($name)->toString());

        return $slug !== '' ? $slug : 'file';
    }
}
