<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Candidate\Domain\Entity\CandidateProfile;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'company')]
#[ORM\UniqueConstraint(name: 'uq_company_slug', columns: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Company implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Company', 'Company.id'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'legal_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Company', 'Company.legalName'])]
    private string $legalName = '';

    #[ORM\Column(name: 'slug', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Company', 'Company.slug'])]
    private string $slug = '';

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['Company', 'Company.status'])]
    private string $status = 'active';

    #[ORM\Column(name: 'main_address', type: Types::TEXT, nullable: true)]
    #[Groups(['Company', 'Company.mainAddress'])]
    private ?string $mainAddress = null;

    /**
     * @var Collection<int, CompanyMembership>
     */
    #[ORM\OneToMany(targetEntity: CompanyMembership::class, mappedBy: 'company', cascade: ['persist'], orphanRemoval: true)]
    private Collection $memberships;

    /**
     * @var Collection<int, CandidateProfile>
     */
    #[ORM\OneToMany(targetEntity: CandidateProfile::class, mappedBy: 'company')]
    private Collection $candidateProfiles;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->memberships = new ArrayCollection();
        $this->candidateProfiles = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getLegalName(): string
    {
        return $this->legalName;
    }

    public function setLegalName(string $legalName): self
    {
        $this->legalName = $legalName;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getMainAddress(): ?string
    {
        return $this->mainAddress;
    }

    public function setMainAddress(?string $mainAddress): self
    {
        $this->mainAddress = $mainAddress;

        return $this;
    }

    /**
     * @return Collection<int, CompanyMembership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }
}
