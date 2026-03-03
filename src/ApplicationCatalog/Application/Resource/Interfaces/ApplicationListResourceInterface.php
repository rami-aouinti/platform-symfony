<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource\Interfaces;

use App\ApplicationCatalog\Application\DTO\Application;
use App\User\Domain\Entity\User;

interface ApplicationListResourceInterface
{
    /**
     * @return Application[]
     */
    public function listCatalog(): array;

    /**
     * @return Application[]
     */
    public function listForUser(User $user): array;
}
