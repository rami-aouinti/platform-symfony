<?php

declare(strict_types=1);

namespace App\PluginCatalog\Domain\Repository\Interfaces;

use App\PluginCatalog\Domain\Entity\Plugin;

interface PluginRepositoryInterface
{
    /**
     * @return Plugin[]
     */
    public function findActiveOrderedByName(): array;
}
