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

#[ORM\Entity]
#[ORM\Table(name: 'resume_language')]
#[ORM\Index(name: 'idx_resume_language_resume_sort', columns: ['resume_id', 'sort_order'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class ResumeLanguage implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Resume::class)]
    #[ORM\JoinColumn(name: 'resume_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Resume $resume = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128)]
    private string $name = '';

    #[ORM\Column(name: 'level', type: Types::STRING, length: 64, nullable: true)]
    private ?string $level = null;

    #[ORM\Column(name: 'sort_order', type: Types::INTEGER, options: [
        'default' => 0,
    ])]
    private int $sortOrder = 0;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getResume(): ?Resume
    {
        return $this->resume;
    }

    public function setResume(?Resume $resume): self
    {
        $this->resume = $resume;

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

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
