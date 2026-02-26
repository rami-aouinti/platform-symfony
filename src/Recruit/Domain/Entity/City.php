<?php

declare(strict_types=1);

namespace App\Recruit\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

/**
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

#[ORM\Entity]
#[ORM\Table(name: 'city')]
#[ORM\UniqueConstraint(name: 'uq_city_region_name', columns: ['region_id', 'name'])]
#[ORM\Index(name: 'idx_city_region', columns: ['region_id'])]
class City implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['City', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Region::class)]
    #[ORM\JoinColumn(name: 'region_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['City', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private ?Region $region = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128, nullable: false)]
    #[Groups(['City', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $name = '';

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

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
}
