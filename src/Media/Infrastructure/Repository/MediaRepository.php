<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Media\Domain\Entity\Media as Entity;
use App\Media\Domain\Repository\Interfaces\MediaRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 */
class MediaRepository extends BaseRepository implements MediaRepositoryInterface
{
    protected static array $searchColumns = ['name', 'path', 'mimeType', 'status'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
