<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package
 * @author
 */
#[ORM\Entity]
#[ORM\Table(name: 'company_membership')]
#[ORM\UniqueConstraint(name: 'uq_company_membership_user_company', columns: ['user_id', 'company_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class CompanyMembership implements EntityInterface
{
    use Timestampable;
    use Uuid;

    final public const string ROLE_OWNER = 'owner';
    final public const string ROLE_MEMBER = 'member';
    final public const string ROLE_CRM_MANAGER = 'crm_manager';
    final public const string ROLE_SHOP_ADMIN = 'shop_admin';
    final public const string ROLE_TEACHER = 'teacher';
    final public const string ROLE_CANDIDATE = 'candidate';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['CompanyMembership', 'CompanyMembership.id'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'companyMemberships')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'memberships')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['CompanyMembership', 'CompanyMembership.company'])]
    private Company $company;

    #[ORM\Column(name: 'role', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['CompanyMembership', 'CompanyMembership.role'])]
    private string $role = self::ROLE_MEMBER;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['CompanyMembership', 'CompanyMembership.status'])]
    private string $status = 'invited';

    #[ORM\Column(name: 'invited_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['CompanyMembership', 'CompanyMembership.invitedAt'])]
    private ?DateTimeImmutable $invitedAt = null;

    #[ORM\Column(name: 'joined_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['CompanyMembership', 'CompanyMembership.joinedAt'])]
    private ?DateTimeImmutable $joinedAt = null;

    public function __construct(User $user, Company $company)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
        $this->company = $company;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getInvitedAt(): ?DateTimeImmutable
    {
        return $this->invitedAt;
    }

    public function setInvitedAt(?DateTimeImmutable $invitedAt): self
    {
        $this->invitedAt = $invitedAt;

        return $this;
    }

    public function getJoinedAt(): ?DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(?DateTimeImmutable $joinedAt): self
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }
}
