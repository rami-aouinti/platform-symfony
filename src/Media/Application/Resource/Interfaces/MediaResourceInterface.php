<?php

declare(strict_types=1);

namespace App\Media\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Media\Domain\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @package App\Media
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

interface MediaResourceInterface extends RestResourceInterface
{
    public function createFromUploadedFile(UploadedFile $file): Media;

    /**
     * @return array{columns: string[], status: string|null, title: string}
     */
    public function resolveExportConfiguration(string $configurationId): array;

    /**
     * @return Media[]
     */
    public function findForExport(?string $status = null): array;
}
