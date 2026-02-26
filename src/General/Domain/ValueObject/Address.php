<?php

declare(strict_types=1);

namespace App\General\Domain\ValueObject;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column(name: 'street_line_1', type: Types::STRING, length: 255, nullable: true)]
    private ?string $streetLine1 = null;

    #[ORM\Column(name: 'street_line_2', type: Types::STRING, length: 255, nullable: true)]
    private ?string $streetLine2 = null;

    #[ORM\Column(name: 'postal_code', type: Types::STRING, length: 32, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(name: 'city', type: Types::STRING, length: 255, nullable: false)]
    private string $city = '';

    #[ORM\Column(name: 'region', type: Types::STRING, length: 255, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(name: 'country_code', type: Types::STRING, length: 2, nullable: false)]
    private string $countryCode = '';

    public function getStreetLine1(): ?string { return $this->streetLine1; }
    public function setStreetLine1(?string $streetLine1): self { $this->streetLine1 = $streetLine1; return $this; }
    public function getStreetLine2(): ?string { return $this->streetLine2; }
    public function setStreetLine2(?string $streetLine2): self { $this->streetLine2 = $streetLine2; return $this; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $postalCode): self { $this->postalCode = $postalCode; return $this; }
    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }
    public function getRegion(): ?string { return $this->region; }
    public function setRegion(?string $region): self { $this->region = $region; return $this; }
    public function getCountryCode(): string { return strtoupper($this->countryCode); }
    public function setCountryCode(string $countryCode): self { $this->countryCode = strtoupper($countryCode); return $this; }
}
