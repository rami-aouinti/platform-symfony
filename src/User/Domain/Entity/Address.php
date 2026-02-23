<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Enum\AddressType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'address')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Address implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: UserProfile::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(name: 'user_profile_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?UserProfile $userProfile = null;

    #[ORM\Column(name: 'type', enumType: AddressType::class, type: Types::STRING, length: 32, nullable: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private AddressType $type = AddressType::OTHER;

    #[ORM\Column(name: 'street_line_1', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private string $streetLine1 = '';

    #[ORM\Column(name: 'street_line_2', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private ?string $streetLine2 = null;

    #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 32, nullable: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private string $postalCode = '';

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private string $city = '';

    #[ORM\Column(name: 'state', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private ?string $state = null;

    #[ORM\Column(name: 'country_code', type: Types::STRING, length: 2, nullable: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private string $countryCode = '';

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    public function setUserProfile(?UserProfile $userProfile): self
    {
        $this->userProfile = $userProfile;

        return $this;
    }

    public function getType(): AddressType
    {
        return $this->type;
    }

    public function setType(AddressType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStreetLine1(): string
    {
        return $this->streetLine1;
    }

    public function setStreetLine1(string $streetLine1): self
    {
        $this->streetLine1 = $streetLine1;

        return $this;
    }

    public function getStreetLine2(): ?string
    {
        return $this->streetLine2;
    }

    public function setStreetLine2(?string $streetLine2): self
    {
        $this->streetLine2 = $streetLine2;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }
}
