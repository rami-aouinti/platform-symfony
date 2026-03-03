<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Transport\Controller\Api\V1\Application;

use App\ApplicationCatalog\Application\Resource\Interfaces\ApplicationListResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[OA\Tag(name: 'Application Catalog')]
final readonly class ApplicationListController
{
    public function __construct(
        private ApplicationListResourceInterface $applicationListResource,
    ) {
    }

    #[Route(path: '/v1/applications', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List public application catalog')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'items' => array_map(
                static fn (\App\ApplicationCatalog\Application\DTO\Application $application): array => $application->toArray(),
                $this->applicationListResource->listCatalog(),
            ),
        ]);
    }
}
