<?php

declare(strict_types=1);

namespace App\Media\Domain\Repository\Interfaces;

use App\Media\Domain\Entity\MediaFolder;
use App\User\Domain\Entity\User;

/**
 * @package App\Media\Domain\Repository\Interfaces
 */
interface MediaFolderRepositoryInterface
{
    public function findRootByOwner(User $owner): ?MediaFolder;

    /** @return MediaFolder[] */
    public function findByOwnerAndParent(User $owner, ?MediaFolder $parent): array;

    /** @return MediaFolder[] */
    public function findChildren(MediaFolder $folder): array;

    public function findAccessible(string $id, User $user, bool $isAdmin): ?MediaFolder;
}
