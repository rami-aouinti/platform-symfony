<?php

declare(strict_types=1);

namespace App\User\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\User
 */
#[ORM\Entity]
#[ORM\Table(name: 'user_profile')]
#[ORM\UniqueConstraint(
    name: 'uq_user_profile_user_id',
    columns: ['user_id'],
)]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class UserProfile implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(
        name: 'id',
        type: UuidBinaryOrderedTimeType::NAME,
        unique: true,
        nullable: false,
    )]
    #[Groups([
        'UserProfile',
        'UserProfile.id',

        'User.userProfile',
    ])]
    private UuidInterface $id;

    #[ORM\OneToOne(
        targetEntity: User::class,
        inversedBy: 'userProfile',
    )]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
    )]
    private User $user;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 255, nullable: true)]
    #[Groups([
        'UserProfile',
        'UserProfile.phone',

        'User.userProfile',
    ])]
    private ?string $phone = null;

    #[ORM\Column(name: 'birth_date', type: Types::DATE_IMMUTABLE, nullable: true)]
    #[Groups([
        'UserProfile',
        'UserProfile.birthDate',

        'User.userProfile',
    ])]
    private ?DateTimeImmutable $birthDate = null;

    #[ORM\Column(name: 'bio', type: Types::TEXT, nullable: true)]
    #[Groups([
        'UserProfile',
        'UserProfile.bio',

        'User.userProfile',
    ])]
    private ?string $bio = null;

    #[ORM\Column(name: 'contacts', type: Types::JSON, nullable: true)]
    #[Groups([
        'UserProfile',
        'UserProfile.contacts',

        'User.userProfile',
    ])]
    private ?array $contacts = null;

    #[ORM\OneToOne(
        targetEntity: UserAvatar::class,
        mappedBy: 'userProfile',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    #[Groups([
        'UserProfile',
        'UserProfile.avatar',

        'User.userProfile',
    ])]
    private ?UserAvatar $avatar = null;

    /**
     * @var Collection<int, Address>
     */
    #[ORM\OneToMany(
        targetEntity: Address::class,
        mappedBy: 'userProfile',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    #[Groups([
        'UserProfile',
        'UserProfile.addresses',

        'User.userProfile',
    ])]
    private Collection $addresses;

    public function __construct(User $user)
    {
        $this->id = $this->createUuid();
        $this->user = $user;
        $this->addresses = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthDate(): ?DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTimeImmutable $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * @return array<mixed>|null
     */
    public function getContacts(): ?array
    {
        return $this->contacts;
    }

    /**
     * @param array<mixed>|null $contacts
     */
    public function setContacts(?array $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }

    public function getAvatar(): ?UserAvatar
    {
        return $this->avatar;
    }

    public function setAvatar(?UserAvatar $avatar): self
    {
        $this->avatar = $avatar;

        if ($avatar instanceof UserAvatar && $avatar->getUserProfile() !== $this) {
            $avatar->setUserProfile($this);
        }

        return $this;
    }

    #[Groups([
        'UserProfile',
        'UserProfile.avatarUrl',

        'User.userProfile',
    ])]
    public function getAvatarUrl(): ?string
    {
        return $this->avatar?->getUrl();
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUserProfile($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address) && $address->getUserProfile() === $this) {
            $address->setUserProfile(null);
        }

        return $this;
    }

    /**
     * @param iterable<int, Address> $addresses
     */
    public function replaceAddresses(iterable $addresses): self
    {
        foreach ($this->addresses as $address) {
            $this->removeAddress($address);
        }

        foreach ($addresses as $address) {
            $this->addAddress($address);
        }

        return $this;
    }
}
