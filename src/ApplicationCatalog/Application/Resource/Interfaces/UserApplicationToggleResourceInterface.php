<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource\Interfaces;

use App\ApplicationCatalog\Application\DTO\Application;
use App\ApplicationCatalog\Domain\Entity\Application as ApplicationEntity;
use App\User\Domain\Entity\User;

interface UserApplicationToggleResourceInterface
{
    public function attach(User $targetUser, ApplicationEntity $application): Application;

    public function toggle(User $targetUser, ApplicationEntity $application, bool $active): Application;

    public function detach(User $targetUser, ApplicationEntity $application): void;
}
