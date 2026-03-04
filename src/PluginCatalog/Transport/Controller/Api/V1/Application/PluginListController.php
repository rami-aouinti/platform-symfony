<?php

declare(strict_types=1);

namespace App\PluginCatalog\Transport\Controller\Api\V1\Application;

use App\PluginCatalog\Application\DTO\Plugin;
use App\PluginCatalog\Application\Resource\Interfaces\PluginListResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

use function array_map;

#[AsController]
#[OA\Tag(name: 'Plugin Catalog')]
final readonly class PluginListController
{
    public function __construct(
        private PluginListResourceInterface $pluginListResource,
    ) {
    }

    #[Route(path: '/v1/plugins', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List public plugin catalog')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'items' => array_map(
                static fn (Plugin $plugin): array => $plugin->toArray(),
                $this->pluginListResource->listCatalog(),
            ),
        ]);
    }
}
