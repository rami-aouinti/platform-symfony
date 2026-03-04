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

    public function attach(User $user, Application $application): UserApplication
    {
        $userApplication = new UserApplication($user, $application);
        $user->addUserApplication($userApplication);

        $this->userApplicationRepository->save($userApplication);

        return $userApplication;
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
        $userApplications = $this->userApplicationRepository->findByUserAndApplication($user, $application);
        $userApplication = $userApplications[0] ?? null;

        if (!$userApplication instanceof UserApplication) {
            $userApplication = new UserApplication($user, $application);
            $user->addUserApplication($userApplication);
            $userApplications = [$userApplication];
        }

        foreach ($userApplications as $item) {
            if ($item->isActive() !== $active) {
                $item->setActive($active);
            }

            $this->userApplicationRepository->save($item);
        }

        return $userApplication;
    }

    public function detach(User $user, Application $application): void
    {
        $userApplications = $this->userApplicationRepository->findByUserAndApplication($user, $application);
        if ($userApplications === []) {
            return;
        }

        foreach ($userApplications as $userApplication) {
            $user->removeUserApplication($userApplication);
            $this->userApplicationRepository->remove($userApplication);
        }
    }
}
