<?php

declare(strict_types=1);

namespace App\Recruit\Application\DTO\Language;

use App\General\Application\DTO\RestDto;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Recruit\Domain\Entity\Language as Entity;
use Override;

class Language extends RestDto
{
    protected string $name = '';
    protected string $code = '';

    public function getName(): string { return $this->name; }
    public function getCode(): string { return $this->code; }

    #[Override]
    public function load(EntityInterface $entity): self
    {
        if ($entity instanceof Entity) {
            $this->id = $entity->getId();
            $this->name = $entity->getName();
            $this->code = $entity->getCode();
        }

        return $this;
    }
}
