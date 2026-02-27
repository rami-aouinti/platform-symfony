<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\General\Domain\Entity\Address as AddressValueObject;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Enum\AddressType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * Address.
 *
 * @package App\User\Domain\Entity
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
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

    #[ORM\Embedded(class: AddressValueObject::class, columnPrefix: false)]
    #[Groups(['UserProfile.addresses', 'User.userProfile'])]
    private AddressValueObject $address;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->address = new AddressValueObject();
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
    public function getAddress(): AddressValueObject
    {
        return $this->address;
    }
    public function setAddress(AddressValueObject $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getStreetLine1(): string
    {
        return $this->address->getStreetLine1() ?? '';
    }
    public function setStreetLine1(string $streetLine1): self
    {
        $this->address->setStreetLine1($streetLine1);

        return $this;
    }
    public function getStreetLine2(): ?string
    {
        return $this->address->getStreetLine2();
    }
    public function setStreetLine2(?string $streetLine2): self
    {
        $this->address->setStreetLine2($streetLine2);

        return $this;
    }
    public function getPostalCode(): string
    {
        return $this->address->getPostalCode() ?? '';
    }
    public function setPostalCode(string $postalCode): self
    {
        $this->address->setPostalCode($postalCode);

        return $this;
    }
    public function getCity(): string
    {
        return $this->address->getCity();
    }
    public function setCity(string $city): self
    {
        $this->address->setCity($city);

        return $this;
    }
    public function getRegion(): ?string
    {
        return $this->address->getRegion();
    }
    public function setRegion(?string $region): self
    {
        $this->address->setRegion($region);

        return $this;
    }
    public function getState(): ?string
    {
        return $this->getRegion();
    }
    public function setState(?string $state): self
    {
        return $this->setRegion($state);
    }
    public function getCountryCode(): string
    {
        return $this->address->getCountryCode();
    }
    public function setCountryCode(string $countryCode): self
    {
        $this->address->setCountryCode($countryCode);

        return $this;
    }
}
