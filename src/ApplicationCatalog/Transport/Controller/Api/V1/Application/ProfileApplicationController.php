<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Transport\Controller\Api\V1\Application;

use App\ApplicationCatalog\Application\DTO\Application;
use App\ApplicationCatalog\Application\DTO\UserApplicationTogglePayload;
use App\ApplicationCatalog\Application\Resource\Interfaces\ApplicationListResourceInterface;
use App\ApplicationCatalog\Application\Resource\Interfaces\UserApplicationToggleResourceInterface;
use App\ApplicationCatalog\Domain\Entity\Application as ApplicationEntity;
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

use function array_map;

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
        $application = $this->findApplicationOrFail($id);

        $payload = UserApplicationTogglePayload::fromPayload(JSON::decode((string)$request->getContent(), true));

        return $this->toggle($application, $payload->isActive());
    }


    #[Route(path: '/v1/profile/applications/{id}/attach', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/applications/{id}/attach', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Attach an application to current user')]
    public function attachAction(string $id): JsonResponse
    {
        $application = $this->findApplicationOrFail($id);
        $dto = $this->userApplicationToggleResource->attach($this->getCurrentUserOrDeny(), $application);

        return new JsonResponse($dto->toArray(), JsonResponse::HTTP_CREATED);
    }

    #[Route(path: '/v1/profile/applications/{id}/activate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/applications/{id}/activate', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Activate an application for current user')]
    public function activateAction(string $id): JsonResponse
    {
        return $this->toggle($this->findApplicationOrFail($id), true);
    }

    #[Route(path: '/v1/profile/applications/{id}/deactivate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/applications/{id}/deactivate', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Deactivate an application for current user')]
    public function deactivateAction(string $id): JsonResponse
    {
        return $this->toggle($this->findApplicationOrFail($id), false);
    }


    #[Route(path: '/v1/profile/applications/{id}/detach', methods: [Request::METHOD_DELETE])]
    #[Route(path: '/v1/me/profile/applications/{id}/detach', methods: [Request::METHOD_DELETE])]
    #[OA\Delete(summary: 'Detach an application from current user')]
    public function detachAction(string $id): JsonResponse
    {
        $this->userApplicationToggleResource->detach($this->getCurrentUserOrDeny(), $this->findApplicationOrFail($id));

        return new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT);
    }

    private function toggle(ApplicationEntity $application, bool $active): JsonResponse
    {
        $dto = $this->userApplicationToggleResource->toggle(
            $this->getCurrentUserOrDeny(),
            $application,
            $active,
        );

        return new JsonResponse($dto->toArray());
    }

    private function findApplicationOrFail(string $id): ApplicationEntity
    {
        $application = $this->applicationRepository->findAdvanced($id);

        if (!$application instanceof ApplicationEntity) {
            throw new NotFoundHttpException('Application not found.');
        }

        return $application;
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
