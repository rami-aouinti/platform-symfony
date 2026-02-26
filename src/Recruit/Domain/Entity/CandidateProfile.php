<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\Candidate
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'candidate_profile')]
#[ORM\UniqueConstraint(name: 'uq_candidate_profile_user', columns: ['user_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class CandidateProfile implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['CandidateProfile', 'CandidateProfile.id'])]
    private UuidInterface $id;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'candidateProfile')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'candidateProfiles')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['CandidateProfile', 'CandidateProfile.company'])]
    private ?Company $company = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['CandidateProfile', 'CandidateProfile.status'])]
    private string $status = 'new';

    public function __construct(User $user)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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
}
