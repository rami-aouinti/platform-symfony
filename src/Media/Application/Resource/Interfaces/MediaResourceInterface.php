<?php

declare(strict_types=1);

namespace App\Media\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Media\Domain\Entity\Media;
use App\Media\Domain\Entity\MediaFolder;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package App\Media\Application\Resource\Interfaces
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface MediaResourceInterface extends RestResourceInterface
{
    public function createFromUploadedFile(UploadedFile $file, ?string $folderId = null): Media;

    public function getOrCreateRootFolder(User $user): MediaFolder;

    /**
     * @return array{columns: string[], status: string|null, title: string}
     */
    public function resolveExportConfiguration(string $configurationId): array;

    /**
     * @return Media[]
     */
    public function findForExport(?string $status = null): array;
}
