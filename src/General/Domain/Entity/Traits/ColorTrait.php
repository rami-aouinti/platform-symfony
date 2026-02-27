<?php

declare(strict_types=1);

namespace App\General\Domain\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * ColorTrait.
 *
 * @package App\General\Domain\Entity\Traits
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
trait ColorTrait
{
    #[ORM\Column(name: 'color', type: Types::STRING, length: 32, nullable: true)]
    private ?string $color = null;

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }
}
