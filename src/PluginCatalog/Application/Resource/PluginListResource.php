<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\Resource;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Application\DTO\Plugin;
use App\PluginCatalog\Application\DTO\PluginMapper;
use App\PluginCatalog\Application\Resource\Interfaces\PluginListResourceInterface;
use App\PluginCatalog\Domain\Repository\Interfaces\PluginRepositoryInterface;
use App\PluginCatalog\Domain\Repository\Interfaces\UserApplicationPluginRepositoryInterface;

final readonly class PluginListResource implements PluginListResourceInterface
{
    public function __construct(
        private PluginRepositoryInterface $pluginRepository,
        private UserApplicationPluginRepositoryInterface $userApplicationPluginRepository,
        private PluginMapper $pluginMapper,
    ) {
    }

    public function listForUserApplication(UserApplication $userApplication): array
    {
        $indexedUserApplicationPlugins = $this->userApplicationPluginRepository
            ->findByUserApplicationIndexedByPluginId($userApplication);
        $items = [];

        foreach ($this->pluginRepository->findActiveOrderedByName() as $plugin) {
            $items[] = $this->pluginMapper->mapEntityToDto(
                $plugin,
                $indexedUserApplicationPlugins[$plugin->getId()] ?? null,
            );
        }

        return $items;
    }
}
