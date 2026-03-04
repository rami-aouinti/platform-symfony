<?php

declare(strict_types=1);

namespace App\Media\Application\Resource;

use App\Media\Application\Resource\Interfaces\MediaFolderResourceInterface;
use App\Media\Domain\Entity\MediaFolder;
use App\Media\Domain\Repository\Interfaces\MediaFolderRepositoryInterface;
use App\Media\Domain\Repository\Interfaces\MediaRepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function array_map;
use function in_array;
use function trim;

class MediaFolderResource implements MediaFolderResourceInterface
{
    public function __construct(
        private readonly MediaFolderRepositoryInterface $mediaFolderRepository,
        private readonly MediaRepositoryInterface $mediaRepository,
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
    }

    public function list(?string $parentId, bool $tree): array
    {
        $user = $this->getCurrentUser();

        if ($tree) {
            $root = $this->getOrCreateRootFolder($user);

            return [$this->buildTreeNode($root, $user)];
        }

        $parent = $parentId !== null ? $this->findOwnedOrAdminFolder($parentId, $user) : $this->getOrCreateRootFolder($user);

        return array_map(fn (MediaFolder $folder): array => $this->toArray($folder), $this->mediaFolderRepository->findByOwnerAndParent($user, $parent));
    }

    public function create(string $name, ?string $parentId): MediaFolder
    {
        $user = $this->getCurrentUser();
        $normalizedName = trim($name);

        if ($normalizedName === '') {
            throw new BadRequestHttpException('Folder name cannot be empty.');
        }

        $parent = $parentId !== null ? $this->findOwnedOrAdminFolder($parentId, $user) : $this->getOrCreateRootFolder($user);

        $folder = (new MediaFolder())
            ->setOwner($user)
            ->setParent($parent)
            ->setName($normalizedName);

        $this->mediaFolderRepository->save($folder);

        return $folder;
    }

    public function rename(string $id, string $name): MediaFolder
    {
        $user = $this->getCurrentUser();
        $folder = $this->findOwnedOrAdminFolder($id, $user);

        if ($this->isRoot($folder)) {
            throw new BadRequestHttpException('Root folder cannot be renamed.');
        }

        $normalizedName = trim($name);

        if ($normalizedName === '') {
            throw new BadRequestHttpException('Folder name cannot be empty.');
        }

        $folder->setName($normalizedName);
        $this->mediaFolderRepository->save($folder);

        return $folder;
    }

    public function delete(string $id, bool $cascade): void
    {
        $user = $this->getCurrentUser();
        $folder = $this->findOwnedOrAdminFolder($id, $user);

        if ($this->isRoot($folder)) {
            throw new BadRequestHttpException('Root folder cannot be deleted.');
        }

        $children = $this->mediaFolderRepository->findChildren($folder);
        $mediaInFolder = $this->mediaRepository->findBy(['folder' => $folder], null, 1);

        if (!$cascade && ($children !== [] || $mediaInFolder !== [])) {
            throw new ConflictHttpException('Folder must be empty before deletion, or use cascade deletion.');
        }

        $this->mediaFolderRepository->remove($folder);
    }

    public function toArray(MediaFolder $folder): array
    {
        return [
            'id' => $folder->getId(),
            'name' => $folder->getName(),
            'parentId' => $folder->getParent()?->getId(),
            'ownerId' => $folder->getOwner()->getId(),
        ];
    }

    private function getOrCreateRootFolder(User $user): MediaFolder
    {
        $rootFolder = $this->mediaFolderRepository->findRootByOwner($user);

        if ($rootFolder instanceof MediaFolder) {
            return $rootFolder;
        }

        $rootFolder = (new MediaFolder())
            ->setOwner($user)
            ->setParent(null)
            ->setName(MediaFolder::ROOT_FOLDER_NAME);

        $this->mediaFolderRepository->save($rootFolder);

        return $rootFolder;
    }

    private function buildTreeNode(MediaFolder $folder, User $user): array
    {
        $children = array_map(
            fn (MediaFolder $child): array => $this->buildTreeNode($child, $user),
            $this->mediaFolderRepository->findByOwnerAndParent($user, $folder),
        );

        return [
            ...$this->toArray($folder),
            'children' => $children,
        ];
    }

    private function findOwnedOrAdminFolder(string $id, User $user): MediaFolder
    {
        $folder = $this->mediaFolderRepository->findAccessible($id, $user, $this->isAdminLike($user));

        if (!$folder instanceof MediaFolder) {
            throw new NotFoundHttpException('Media folder not found.');
        }

        if (!$this->isAdminLike($user) && $folder->getOwner()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException('Only folder owner can manage this folder.');
        }

        return $folder;
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }

    private function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    private function isRoot(MediaFolder $folder): bool
    {
        return $folder->getParent() === null && $folder->getName() === MediaFolder::ROOT_FOLDER_NAME;
    }
}
