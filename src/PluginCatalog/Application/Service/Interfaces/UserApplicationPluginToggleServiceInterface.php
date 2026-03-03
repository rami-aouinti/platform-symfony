<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\Service\Interfaces;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Domain\Entity\Plugin;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin;

interface UserApplicationPluginToggleServiceInterface
{
    public function activate(UserApplication $userApplication, Plugin $plugin): UserApplicationPlugin;

    public function deactivate(UserApplication $userApplication, Plugin $plugin): UserApplicationPlugin;

    public function toggle(UserApplication $userApplication, Plugin $plugin, bool $active): UserApplicationPlugin;
}
