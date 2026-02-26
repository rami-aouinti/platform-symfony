<?php

declare(strict_types=1);

namespace App\General\Domain\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait SlugTrait
{
    #[ORM\Column(name: 'slug', type: Types::STRING, length: 255, nullable: false)]
    private string $slug = '';

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
