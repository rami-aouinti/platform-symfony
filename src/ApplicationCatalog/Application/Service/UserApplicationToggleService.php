<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Service;

use App\ApplicationCatalog\Application\Service\Interfaces\UserApplicationToggleServiceInterface;
use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\ApplicationCatalog\Domain\Repository\Interfaces\UserApplicationRepositoryInterface;
use App\User\Domain\Entity\User;

class UserApplicationToggleService implements UserApplicationToggleServiceInterface
{
    public function __construct(
        private readonly UserApplicationRepositoryInterface $userApplicationRepository,
    ) {
    }

    public function activate(User $user, Application $application): UserApplication
    {
        return $this->toggle($user, $application, true);
    }

    public function deactivate(User $user, Application $application): UserApplication
    {
        return $this->toggle($user, $application, false);
    }

    public function toggle(User $user, Application $application, bool $active): UserApplication
    {
        $userApplication = $this->userApplicationRepository->findOneByUserAndApplication($user, $application);

        if (!$userApplication instanceof UserApplication) {
            $userApplication = new UserApplication($user, $application);
            $user->addUserApplication($userApplication);
        }

        if ($userApplication->isActive() !== $active) {
            $userApplication->setActive($active);
        }

        $this->userApplicationRepository->save($userApplication);

        return $userApplication;
    }
}
