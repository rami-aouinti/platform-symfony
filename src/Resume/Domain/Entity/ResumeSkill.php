<?php

declare(strict_types=1);

namespace App\Resume\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\Resume\Domain\Enum\ResumeSkillLevel;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'resume_skill')]
#[ORM\Index(name: 'idx_resume_skill_resume_sort', columns: ['resume_id', 'sort_order'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class ResumeSkill implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['ResumeSkill', 'ResumeSkill.id', 'ResumeSkill.show', 'ResumeSkill.edit'])]
    private UuidInterface $id;

    #[ORM\ManyToOne(targetEntity: Resume::class)]
    #[ORM\JoinColumn(name: 'resume_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Groups(['ResumeSkill', 'ResumeSkill.resume', 'ResumeSkill.create', 'ResumeSkill.show', 'ResumeSkill.edit'])]
    private ?Resume $resume = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 128)]
    #[Groups(['ResumeSkill', 'ResumeSkill.name', 'ResumeSkill.create', 'ResumeSkill.show', 'ResumeSkill.edit'])]
    private string $name = '';

    #[ORM\Column(name: 'level', type: Types::STRING, length: 32, enumType: ResumeSkillLevel::class)]
    #[Groups(['ResumeSkill', 'ResumeSkill.level', 'ResumeSkill.create', 'ResumeSkill.show', 'ResumeSkill.edit'])]
    private ResumeSkillLevel $level = ResumeSkillLevel::INTERMEDIATE;

    #[ORM\Column(name: 'years_experience', type: Types::SMALLINT, nullable: true)]
    #[Groups(['ResumeSkill', 'ResumeSkill.yearsExperience', 'ResumeSkill.create', 'ResumeSkill.show', 'ResumeSkill.edit'])]
    private ?int $yearsExperience = null;

    #[ORM\Column(name: 'sort_order', type: Types::INTEGER, options: ['default' => 0])]
    #[Groups(['ResumeSkill', 'ResumeSkill.sortOrder', 'ResumeSkill.create', 'ResumeSkill.show', 'ResumeSkill.edit'])]
    private int $sortOrder = 0;

    public function __construct()
    {
        $this->id = $this->createUuid();
    }

    public function getId(): string { return $this->id->toString(); }
    public function getResume(): ?Resume { return $this->resume; }
    public function setResume(?Resume $resume): self { $this->resume = $resume; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getLevel(): ResumeSkillLevel { return $this->level; }
    public function setLevel(ResumeSkillLevel|string $level): self { $this->level = $level instanceof ResumeSkillLevel ? $level : ResumeSkillLevel::from($level); return $this; }
    public function getYearsExperience(): ?int { return $this->yearsExperience; }
    public function setYearsExperience(?int $yearsExperience): self { $this->yearsExperience = $yearsExperience; return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): self { $this->sortOrder = $sortOrder; return $this; }
}
