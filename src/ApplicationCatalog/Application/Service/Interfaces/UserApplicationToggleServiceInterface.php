<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Service\Interfaces;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\User\Domain\Entity\User;

interface UserApplicationToggleServiceInterface
{
    public function attach(User $user, Application $application): UserApplication;

    public function activate(User $user, Application $application): UserApplication;

    public function deactivate(User $user, Application $application): UserApplication;

    public function toggle(User $user, Application $application, bool $active): UserApplication;

    public function detach(User $user, Application $application): void;
}
