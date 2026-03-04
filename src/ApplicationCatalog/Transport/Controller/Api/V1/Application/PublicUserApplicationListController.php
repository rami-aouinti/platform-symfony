<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Transport\Controller\Api\V1\Application;

use App\ApplicationCatalog\Application\DTO\UserApplication;
use App\ApplicationCatalog\Application\Resource\Interfaces\PublicUserApplicationListResourceInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

use function array_map;

#[AsController]
#[OA\Tag(name: 'Application Catalog')]
final readonly class PublicUserApplicationListController
{
    public function __construct(
        private PublicUserApplicationListResourceInterface $publicUserApplicationListResource,
        private UserTypeIdentification $userTypeIdentification,
    ) {
    }

    #[Route(path: '/v1/user-applications', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List all user applications (public), with owner flag when authenticated')]
    public function __invoke(): JsonResponse
    {
        $currentUser = $this->userTypeIdentification->getUser();

        return new JsonResponse([
            'items' => array_map(
                static fn (UserApplication $userApplication): array => $userApplication->toArray(),
                $this->publicUserApplicationListResource->list($currentUser instanceof User ? $currentUser : null),
            ),
        ]);
    }
}
