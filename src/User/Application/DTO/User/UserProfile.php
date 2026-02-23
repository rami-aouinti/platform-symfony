<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\User\Domain\Entity\UserProfile as Entity;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\User
 */
class UserProfile
{
    #[Assert\Length(max: 255)]
    protected ?string $photo = null;

    #[Assert\Length(max: 255)]
    protected ?string $phone = null;

    protected ?DateTimeImmutable $birthDate = null;

    protected ?string $bio = null;

    #[Assert\Length(max: 255)]
    protected ?string $address = null;

    /**
     * @var array<mixed>|null
     */
    protected ?array $contacts = null;

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

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

    public static function fromEntity(Entity $entity): self
    {
        return (new self())
            ->setPhoto($entity->getPhoto())
            ->setPhone($entity->getPhone())
            ->setBirthDate($entity->getBirthDate())
            ->setBio($entity->getBio())
            ->setAddress($entity->getAddress())
            ->setContacts($entity->getContacts());
    }
}
