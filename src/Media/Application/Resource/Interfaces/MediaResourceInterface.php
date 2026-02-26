<?php

declare(strict_types=1);

namespace App\Media\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Media\Domain\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface MediaResourceInterface extends RestResourceInterface
{
    public function createFromUploadedFile(UploadedFile $file): Media;
}
