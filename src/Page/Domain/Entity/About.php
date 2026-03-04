<?php

declare(strict_types=1);

namespace App\Page\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'page_about')]
#[ORM\UniqueConstraint(name: 'uq_page_about_name', columns: ['name'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class About implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['About', 'About.id', 'About.show', 'About.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['About', 'About.name', 'About.create', 'About.show', 'About.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: false)]
    #[Groups(['About', 'About.description', 'About.create', 'About.show', 'About.edit'])]
    private string $description = '';

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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
