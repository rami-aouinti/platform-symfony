<?php

declare(strict_types=1);

namespace App\Resume\Infrastructure\Repository;

use App\General\Infrastructure\Repository\BaseRepository;
use App\Resume\Domain\Entity\ResumeEducation as Entity;
use App\Resume\Domain\Repository\Interfaces\ResumeEducationRepositoryInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ResumeEducationRepository extends BaseRepository implements ResumeEducationRepositoryInterface
{
    protected static array $searchColumns = ['schoolName', 'degree', 'fieldOfStudy', 'description'];
    protected static string $entityName = Entity::class;

    public function __construct(protected ManagerRegistry $managerRegistry)
    {
    }
}
