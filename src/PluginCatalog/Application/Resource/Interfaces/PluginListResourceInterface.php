<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\Resource\Interfaces;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Application\DTO\Plugin;

interface PluginListResourceInterface
{
    /**
     * @return Plugin[]
     */
    public function listForUserApplication(UserApplication $userApplication): array;

    /**
     * @return Plugin[]
     */
    public function listCatalog(): array;
}
