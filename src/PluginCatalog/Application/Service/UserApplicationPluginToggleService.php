<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\Service;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Application\Service\Interfaces\UserApplicationPluginToggleServiceInterface;
use App\PluginCatalog\Domain\Entity\Plugin;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin;
use App\PluginCatalog\Domain\Repository\Interfaces\UserApplicationPluginRepositoryInterface;

class UserApplicationPluginToggleService implements UserApplicationPluginToggleServiceInterface
{
    public function __construct(
        private readonly UserApplicationPluginRepositoryInterface $userApplicationPluginRepository,
    ) {
    }

    public function activate(UserApplication $userApplication, Plugin $plugin): UserApplicationPlugin
    {
        return $this->toggle($userApplication, $plugin, true);
    }

    public function deactivate(UserApplication $userApplication, Plugin $plugin): UserApplicationPlugin
    {
        return $this->toggle($userApplication, $plugin, false);
    }

    public function toggle(UserApplication $userApplication, Plugin $plugin, bool $active): UserApplicationPlugin
    {
        $userApplicationPlugin = $this->userApplicationPluginRepository
            ->findOneByUserApplicationAndPlugin($userApplication, $plugin);

        if (!$userApplicationPlugin instanceof UserApplicationPlugin) {
            $userApplicationPlugin = new UserApplicationPlugin($userApplication, $plugin);
        }

        if ($userApplicationPlugin->isActive() !== $active) {
            $userApplicationPlugin->setActive($active);
        }

        $this->userApplicationPluginRepository->save($userApplicationPlugin);

        return $userApplicationPlugin;
    }
}
