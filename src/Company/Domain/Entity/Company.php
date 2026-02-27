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
 * @package App\Company\Domain\Entity
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

    #[ORM\Embedded(class: AddressValueObject::class, columnPrefix: 'main_address_')]
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

    #[ORM\Column(name: 'photo_url', type: Types::STRING, length: 2048, nullable: true)]
    #[Groups(['Company', 'Company.photoUrl', 'Company.create', 'Company.show', 'Company.edit'])]
    private ?string $photoUrl = null;

    #[ORM\Column(name: 'photo_media_id', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['Company', 'Company.photoMediaId', 'Company.create', 'Company.show', 'Company.edit'])]
    private ?string $photoMediaId = null;

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

    public function getPhotoMediaId(): ?string
    {
        return $this->photoMediaId;
    }

    public function setPhotoMediaId(?string $photoMediaId): self
    {
        $this->photoMediaId = $photoMediaId;

        return $this;
    }

    public function setPhotoUrl(?string $photoUrl): self
    {
        $this->photoUrl = $photoUrl;

        return $this;
    }

    public function getStoredPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    #[Groups(['Company', 'Company.photoUrl', 'Company.show'])]
    public function getPhotoUrl(): string
    {
        return $this->photoUrl ?? sprintf(
            'https://ui-avatars.com/api/?name=%s&format=png',
            rawurlencode($this->legalName),
        );
    }

    #[Groups(['Company', 'Company.photo', 'Company.show'])]
    public function getPhoto(): string
    {
        return $this->getPhotoUrl();
    }

    #[Groups(['Company', 'Company.image', 'Company.show'])]
    public function getImage(): string
    {
        return $this->getPhotoUrl();
    }
}
