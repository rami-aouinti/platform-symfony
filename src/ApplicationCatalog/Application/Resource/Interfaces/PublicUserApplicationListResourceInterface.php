<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource\Interfaces;

use App\ApplicationCatalog\Application\DTO\UserApplication;
use App\User\Domain\Entity\User;

interface PublicUserApplicationListResourceInterface
{
    /**
     * @return UserApplication[]
     */
    public function list(?User $currentUser = null): array;
}
