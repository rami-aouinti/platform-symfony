<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Media\Domain\Entity\MediaFolder as Entity;
use App\Media\Domain\Entity\MediaFolder;
use App\Media\Domain\Repository\Interfaces\MediaFolderRepositoryInterface;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Media\Infrastructure\Repository
 */
class MediaFolderRepository extends BaseRepository implements MediaFolderRepositoryInterface
{
    protected static array $searchColumns = ['name'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findRootByOwner(User $owner): ?MediaFolder
    {
        $folder = $this->findOneBy([
            'owner' => $owner,
            'parent' => null,
            'name' => MediaFolder::ROOT_FOLDER_NAME,
        ]);

        return $folder instanceof MediaFolder ? $folder : null;
    }

    public function findByOwnerAndParent(User $owner, ?MediaFolder $parent): array
    {
        return $this->findBy(
            [
                'owner' => $owner,
                'parent' => $parent,
            ],
            ['name' => 'ASC'],
        );
    }

    public function findChildren(MediaFolder $folder): array
    {
        return $this->findBy([
            'parent' => $folder,
        ]);
    }

    public function findAccessible(string $id, User $user, bool $isAdmin): ?MediaFolder
    {
        $folder = $this->find($id);

        if (!$folder instanceof MediaFolder) {
            return null;
        }

        if ($isAdmin || $folder->getOwner()->getId() === $user->getId()) {
            return $folder;
        }

        return null;
    }
}
