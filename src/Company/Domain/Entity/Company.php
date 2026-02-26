<?php

declare(strict_types=1);

namespace App\Company\Domain\Entity;

use App\Company\Domain\Enum\CompanyStatus;
use App\General\Domain\Entity\Address as AddressValueObject;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\SlugTrait;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Recruit\Domain\Entity\CandidateProfile;
use App\User\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\Company
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'company')]
#[ORM\UniqueConstraint(name: 'uq_company_slug', columns: ['slug'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Company implements EntityInterface
{
    use SlugTrait;
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Company', 'Company.id', 'Company.show', 'Company.edit', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'legal_name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Company', 'Company.legalName', 'Company.create', 'Company.show', 'Company.edit', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $legalName = '';

    #[ORM\Embedded(class: AddressValueObject::class, columnPrefix: false)]
    #[ORM\AttributeOverrides([
        new ORM\AttributeOverride(name: 'streetLine1', column: new ORM\Column(name: 'main_address_street_line_1', type: Types::STRING, length: 255, nullable: true)),
        new ORM\AttributeOverride(name: 'streetLine2', column: new ORM\Column(name: 'main_address_street_line_2', type: Types::STRING, length: 255, nullable: true)),
        new ORM\AttributeOverride(name: 'postalCode', column: new ORM\Column(name: 'main_address_postal_code', type: Types::STRING, length: 32, nullable: true)),
        new ORM\AttributeOverride(name: 'city', column: new ORM\Column(name: 'main_address_city', type: Types::STRING, length: 255, nullable: false)),
        new ORM\AttributeOverride(name: 'region', column: new ORM\Column(name: 'main_address_region', type: Types::STRING, length: 255, nullable: true)),
        new ORM\AttributeOverride(name: 'countryCode', column: new ORM\Column(name: 'main_address_country_code', type: Types::STRING, length: 2, nullable: false)),
    ])]
    #[Groups(['Company', 'Company.mainAddress', 'Company.create', 'Company.show', 'Company.edit'])]
    private AddressValueObject $mainAddress;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Company', 'Company.owner', 'Company.show'])]
    private ?User $owner = null;

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

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, enumType: CompanyStatus::class)]
    #[Groups(['Company', 'Company.status', 'Company.create', 'Company.show', 'Company.edit'])]
    private CompanyStatus $status = CompanyStatus::ACTIVE;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->memberships = new ArrayCollection();
        $this->candidateProfiles = new ArrayCollection();
        $this->mainAddress = new AddressValueObject();
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

    public function getMainAddress(): AddressValueObject
    {
        return $this->mainAddress;
    }

    public function setMainAddress(AddressValueObject $mainAddress): self
    {
        $this->mainAddress = $mainAddress;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, CompanyMembership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(CompanyMembership $membership): self
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
        }

        return $this;
    }

    public function getStatus(): CompanyStatus
    {
        return $this->status;
    }

    public function setStatus(CompanyStatus|string $status): self
    {
        $nextStatus = $status instanceof CompanyStatus ? $status : CompanyStatus::from($status);

        if (!$this->status->canTransitionTo($nextStatus) && $this->status !== $nextStatus) {
            return $this;
        }

        $this->status = $nextStatus;

        return $this;
    }
}

