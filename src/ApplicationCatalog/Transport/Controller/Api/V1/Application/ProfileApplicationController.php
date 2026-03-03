<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Transport\Controller\Api\V1\Application;

use App\ApplicationCatalog\Application\DTO\Application;
use App\ApplicationCatalog\Application\DTO\UserApplicationTogglePayload;
use App\ApplicationCatalog\Application\Resource\Interfaces\ApplicationListResourceInterface;
use App\ApplicationCatalog\Application\Resource\Interfaces\UserApplicationToggleResourceInterface;
use App\ApplicationCatalog\Infrastructure\Repository\ApplicationRepository;
use App\General\Domain\Utils\JSON;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use JsonException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[OA\Tag(name: 'Me/Profile - Application')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
final readonly class ProfileApplicationController
{
    public function __construct(
        private ApplicationListResourceInterface $applicationListResource,
        private UserApplicationToggleResourceInterface $userApplicationToggleResource,
        private UserTypeIdentification $userTypeIdentification,
        private ApplicationRepository $applicationRepository,
    ) {
    }

    #[Route(path: '/v1/profile/applications', methods: [Request::METHOD_GET])]
    #[Route(path: '/v1/me/profile/applications', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List applications and activation state for current user')]
    public function __invoke(): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();

        return new JsonResponse([
            'items' => array_map(
                static fn (Application $application): array => $application->toArray(),
                $this->applicationListResource->listForUser($user),
            ),
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/profile/applications/{id}', methods: [Request::METHOD_PATCH])]
    #[Route(path: '/v1/me/profile/applications/{id}', methods: [Request::METHOD_PATCH])]
    #[OA\Patch(summary: 'Toggle application activation for current user')]
    public function patchAction(Request $request, string $id): JsonResponse
    {
        $application = $this->applicationRepository->find($id);

        if (!$application instanceof \App\ApplicationCatalog\Domain\Entity\Application) {
            throw new NotFoundHttpException('Application not found.');
        }

        $payload = UserApplicationTogglePayload::fromPayload(JSON::decode((string)$request->getContent(), true));
        $dto = $this->userApplicationToggleResource->toggle(
            $this->getCurrentUserOrDeny(),
            $application,
            $payload->isActive(),
        );

        return new JsonResponse($dto->toArray());
    }

    private function getCurrentUserOrDeny(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
