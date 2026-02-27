<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Recruit\Domain\Entity\ResumeSkill as Entity;
use App\Recruit\Domain\Repository\Interfaces\ResumeSkillRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Recruit\Infrastructure\Repository
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ResumeSkillRepository extends BaseRepository implements ResumeSkillRepositoryInterface
{
    protected static array $searchColumns = ['name'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry
    ) {
    }
}
