<?php

declare(strict_types=1);

namespace App\General\Domain\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait DescriptionTrait
{
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: false)]
    private string $description = '';

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
