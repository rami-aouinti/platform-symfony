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
#[ORM\Table(name: 'skill')]
#[ORM\UniqueConstraint(name: 'uq_skill_name', columns: ['name'])]
class Skill implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Skill', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 100, nullable: false)]
    #[Groups(['Skill', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $name = '';

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
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
