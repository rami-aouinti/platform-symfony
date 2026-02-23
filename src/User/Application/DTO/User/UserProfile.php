<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\User\Domain\Entity\UserProfile as Entity;
use Symfony\Component\Validator\Constraints as Assert;

use function array_map;

/**
 * @package App\User
 */
class UserProfile
{
    #[Assert\Length(max: 255)]
    #[Assert\Regex(pattern: '/^\+?[1-9]\d{6,14}$/')]
    protected ?string $phone = null;

    protected ?\DateTimeImmutable $birthDate = null;

    protected ?string $bio = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $contacts = null;

    #[Assert\Valid]
    protected ?UserAvatar $avatar = null;

    /**
     * @var array<int, Address>
     */
    #[Assert\Valid]
    protected array $addresses = [];

    protected ?string $avatarUrl = null;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeImmutable $birthDate): self
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

        return $this;
    }

    /**
     * @return array<int, Address>
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * @param array<int, Address> $addresses
     */
    public function setAddresses(array $addresses): self
    {
        $this->addresses = $addresses;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public static function fromEntity(Entity $entity): self
    {
        /** @var array<int, Address> $addresses */
        $addresses = array_map(
            static fn (\App\User\Domain\Entity\Address $address): Address => Address::fromEntity($address),
            $entity->getAddresses()->toArray(),
        );

        return (new self())
            ->setPhone($entity->getPhone())
            ->setBirthDate($entity->getBirthDate())
            ->setBio($entity->getBio())
            ->setContacts($entity->getContacts())
            ->setAvatar($entity->getAvatar() ? UserAvatar::fromEntity($entity->getAvatar()) : null)
            ->setAvatarUrl($entity->getAvatarUrl())
            ->setAddresses($addresses);
    }
}
