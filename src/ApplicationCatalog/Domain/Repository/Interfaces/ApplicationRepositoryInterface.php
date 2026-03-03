<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Domain\Repository\Interfaces;

use App\ApplicationCatalog\Domain\Entity\Application;

interface ApplicationRepositoryInterface
{
    public function findOneByName(string $name): ?Application;

    /**
     * @return Application[]
     */
    public function findAllOrderedByName(): array;

    /**
     * @return Application[]
     */
    public function findActiveOrderedByName(): array;
}
