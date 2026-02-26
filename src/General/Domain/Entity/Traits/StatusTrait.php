<?php

declare(strict_types=1);

namespace App\General\Domain\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait StatusTrait
{
    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false)]
    private string $status = '';

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
