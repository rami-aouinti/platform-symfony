<?php

declare(strict_types=1);

namespace App\JobOffer\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'region')]
#[ORM\UniqueConstraint(name: 'uq_region_country_code', columns: ['country_code', 'code'])]
class Region implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Region', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['Region', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $code = '';

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128, nullable: false)]
    #[Groups(['Region', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'country_code', type: Types::STRING, length: 2, nullable: false)]
    #[Groups(['Region', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $countryCode = '';

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
