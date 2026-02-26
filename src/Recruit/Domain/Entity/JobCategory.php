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
#[ORM\Table(name: 'job_category')]
#[ORM\UniqueConstraint(name: 'uq_job_category_code', columns: ['code'])]
class JobCategory implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['JobCategory', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 64, nullable: false)]
    #[Groups(['JobCategory', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $code = '';

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128, nullable: false)]
    #[Groups(['JobCategory', 'JobOffer', 'JobOffer.show', 'JobOffer.edit'])]
    private string $name = '';

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
}
