<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\Repository;

use App\Blog\Domain\Entity\BlogPostLink as Entity;
use App\Blog\Domain\Repository\Interfaces\BlogPostLinkRepositoryInterface;
use App\General\Infrastructure\Repository\BaseRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Blog\Infrastructure\Repository
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class BlogPostLinkRepository extends BaseRepository implements BlogPostLinkRepositoryInterface
{
    protected static array $searchColumns = ['referenceType'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }
}
