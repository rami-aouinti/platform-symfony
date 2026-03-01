<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\Skill;

use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Skill as Entity;
use Override;

class Skill extends RestDto
{
    protected string $name = '';

    public function getName(): string { return $this->name; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->name = $entity->getName();
        }

        return $this;
    }
}
