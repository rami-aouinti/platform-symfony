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
#[ORM\Table(name: 'page_faq')]
#[ORM\Index(name: 'idx_page_faq_order', columns: ['display_order'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Faq implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Faq', 'Faq.id', 'Faq.show', 'Faq.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Faq', 'Faq.name', 'Faq.create', 'Faq.show', 'Faq.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: false)]
    #[Groups(['Faq', 'Faq.description', 'Faq.create', 'Faq.show', 'Faq.edit'])]
    private string $description = '';

    #[ORM\Column(name: 'display_order', type: Types::INTEGER, nullable: false, options: ['default' => 0])]
    #[Groups(['Faq', 'Faq.order', 'Faq.create', 'Faq.show', 'Faq.edit'])]
    private int $order = 0;

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

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }
}
