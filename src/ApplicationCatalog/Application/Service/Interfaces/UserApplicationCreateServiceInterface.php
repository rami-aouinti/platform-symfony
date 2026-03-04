<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Service\Interfaces;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\User\Domain\Entity\User;

interface UserApplicationCreateServiceInterface
{
    public function create(User $user, Application $application, ?string $name, ?string $logo, ?string $description, bool $public): UserApplication;
}
