<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\Resource\Interfaces;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Application\DTO\Plugin;
use App\PluginCatalog\Domain\Entity\Plugin as PluginEntity;

interface UserApplicationPluginToggleResourceInterface
{
    public function attach(UserApplication $userApplication, PluginEntity $plugin): Plugin;

    public function toggle(UserApplication $userApplication, PluginEntity $plugin, bool $active): Plugin;

    public function detach(UserApplication $userApplication, PluginEntity $plugin): void;
}
