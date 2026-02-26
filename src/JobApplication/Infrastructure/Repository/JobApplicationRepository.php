<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\Repository;

use App\Company\Domain\Entity\CompanyMembership;
use App\General\Infrastructure\Repository\BaseRepository;
use App\JobApplication\Domain\Entity\JobApplication as Entity;
use App\JobApplication\Domain\Repository\Interfaces\JobApplicationRepositoryInterface;
use App\Role\Domain\Enum\Role;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

use function in_array;

/**
 * @method Entity|null find(string $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?string $entityManagerName = null)
 * @method Entity[] findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?string $entityManagerName = null)
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class JobApplicationRepository extends BaseRepository implements JobApplicationRepositoryInterface
{
    protected static array $searchColumns = ['status'];
    protected static string $entityName = Entity::class;

    public function __construct(
        protected ManagerRegistry $managerRegistry,
    ) {
    }

    public function findForMyOffers(User $user): array
    {
        if (in_array(Role::ROOT->value, $user->getRoles(), true) || in_array(Role::ADMIN->value, $user->getRoles(), true)) {
            return $this->createQueryBuilder('application')
                ->addSelect('jobOffer', 'company', 'candidate', 'decidedBy')
                ->join('application.jobOffer', 'jobOffer')
                ->leftJoin('jobOffer.company', 'company')
                ->leftJoin('application.candidate', 'candidate')
                ->leftJoin('application.decidedBy', 'decidedBy')
                ->orderBy('application.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->createQueryBuilder('application')
            ->addSelect('jobOffer', 'company', 'candidate', 'decidedBy', 'membership')
            ->join('application.jobOffer', 'jobOffer')
            ->leftJoin('jobOffer.company', 'company')
            ->leftJoin('application.candidate', 'candidate')
            ->leftJoin('application.decidedBy', 'decidedBy')
            ->leftJoin(
                CompanyMembership::class,
                'membership',
                Join::WITH,
                'membership.company = company AND membership.user = :user AND membership.role IN (:decisionRoles)',
            )
            ->where('jobOffer.createdBy = :user OR company.owner = :user OR membership.id IS NOT NULL')
            ->setParameter('user', $user)
            ->setParameter('decisionRoles', [CompanyMembership::ROLE_OWNER, CompanyMembership::ROLE_CRM_MANAGER])
            ->orderBy('application.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
