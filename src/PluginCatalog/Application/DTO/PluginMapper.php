<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\DTO;

use App\PluginCatalog\Domain\Entity\Plugin as PluginEntity;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin;

final class PluginMapper
{
    public function mapEntityToDto(PluginEntity $plugin, ?UserApplicationPlugin $userApplicationPlugin = null): Plugin
    {
        return new Plugin(
            id: $plugin->getId(),
            keyName: $plugin->getKeyName(),
            name: $plugin->getName(),
            logo: $plugin->getLogo(),
            description: $plugin->getDescription(),
            active: $plugin->isActive(),
            enabled: $userApplicationPlugin?->isActive(),
        );
    }
}
