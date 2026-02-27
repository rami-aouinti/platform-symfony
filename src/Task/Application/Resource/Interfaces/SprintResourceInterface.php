<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\Sprint;

interface SprintResourceInterface extends RestResourceInterface
{
    /**
     * @return array<int, Sprint>
     */
    public function findByCompany(string $companyId, ?bool $active = null): array;
}
