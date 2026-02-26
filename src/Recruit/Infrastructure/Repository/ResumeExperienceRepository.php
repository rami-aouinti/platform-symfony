<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Recruit\Domain\Entity\ResumeExperience as Entity;
use App\Recruit\Domain\Repository\Interfaces\ResumeExperienceRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ResumeExperienceRepository extends BaseRepository implements ResumeExperienceRepositoryInterface
{
    protected static array $searchColumns = ['title', 'companyName', 'description'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry
    ) {
    }
}
