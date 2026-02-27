<?php

declare(strict_types=1);

namespace App\User\Application\DTO\User;

use App\User\Domain\Entity\Address as Entity;
use App\User\Domain\Enum\AddressType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\User\Application\DTO\User
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

class Address
{
    #[Assert\NotBlank]
    protected AddressType $type = AddressType::OTHER;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    protected string $streetLine1 = '';

    #[Assert\Length(max: 255)]
    protected ?string $streetLine2 = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[\p{L}\p{N}\-\s]{3,16}$/u')]
    protected string $postalCode = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    protected string $city = '';

    #[Assert\Length(max: 255)]
    protected ?string $region = null;

    #[Assert\NotBlank]
    #[Assert\Country]
    protected string $countryCode = '';

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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

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
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public static function fromEntity(Entity $entity): self
    {
        return (new self())
            ->setType($entity->getType())
            ->setStreetLine1($entity->getStreetLine1())
            ->setStreetLine2($entity->getStreetLine2())
            ->setPostalCode($entity->getPostalCode())
            ->setCity($entity->getCity())
            ->setRegion($entity->getRegion())
            ->setCountryCode($entity->getCountryCode());
    }
}
