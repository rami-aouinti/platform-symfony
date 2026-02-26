<?php

declare(strict_types=1);

namespace App\General\Application\DTO;

use App\General\Domain\Entity\Address as AddressValueObject;
use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    #[Assert\Length(max: 255)]
    protected ?string $streetLine1 = null;
    #[Assert\Length(max: 255)]
    protected ?string $streetLine2 = null;
    #[Assert\Length(max: 32)]
    protected ?string $postalCode = null;
    #[Assert\NotBlank]
    protected string $city = '';
    #[Assert\Length(max: 255)]
    protected ?string $region = null;
    #[Assert\NotBlank]
    #[Assert\Country]
    protected string $countryCode = '';

    public function toValueObject(): AddressValueObject
    {
        return (new AddressValueObject())
            ->setStreetLine1($this->streetLine1)
            ->setStreetLine2($this->streetLine2)
            ->setPostalCode($this->postalCode)
            ->setCity($this->city)
            ->setRegion($this->region)
            ->setCountryCode($this->countryCode);
    }

    public static function fromValueObject(AddressValueObject $address): self
    {
        $dto = new self();
        $dto->streetLine1 = $address->getStreetLine1();
        $dto->streetLine2 = $address->getStreetLine2();
        $dto->postalCode = $address->getPostalCode();
        $dto->city = $address->getCity();
        $dto->region = $address->getRegion();
        $dto->countryCode = $address->getCountryCode();

        return $dto;
    }

    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }
    public function getRegion(): ?string { return $this->region; }
    public function setRegion(?string $region): self { $this->region = $region; return $this; }
    public function getCountryCode(): string { return $this->countryCode; }
    public function setCountryCode(string $countryCode): self { $this->countryCode = $countryCode; return $this; }
    public function getStreetLine1(): ?string { return $this->streetLine1; }
    public function setStreetLine1(?string $streetLine1): self { $this->streetLine1 = $streetLine1; return $this; }
    public function getStreetLine2(): ?string { return $this->streetLine2; }
    public function setStreetLine2(?string $streetLine2): self { $this->streetLine2 = $streetLine2; return $this; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $postalCode): self { $this->postalCode = $postalCode; return $this; }
}
