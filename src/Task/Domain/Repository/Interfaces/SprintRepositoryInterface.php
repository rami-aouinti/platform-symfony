<?php

declare(strict_types=1);

namespace App\Task\Domain\Repository\Interfaces;

use App\Task\Domain\Entity\Sprint;

interface SprintRepositoryInterface
{
    /**
     * @return array<int, Sprint>
     */
    public function findByCompany(string $companyId, ?bool $active = null): array;
}
