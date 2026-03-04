<?php

declare(strict_types=1);

namespace App\PluginCatalog\Transport\Controller\Api\V1\Application;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\ApplicationCatalog\Infrastructure\Repository\UserApplicationRepository;
use App\General\Domain\Rest\UuidHelper;
use App\General\Domain\Utils\JSON;
use App\PluginCatalog\Application\DTO\Plugin;
use App\PluginCatalog\Application\DTO\UserApplicationPluginTogglePayload;
use App\PluginCatalog\Application\Resource\Interfaces\PluginListResourceInterface;
use App\PluginCatalog\Application\Resource\Interfaces\UserApplicationPluginToggleResourceInterface;
use App\PluginCatalog\Domain\Entity\Plugin as PluginEntity;
use App\PluginCatalog\Infrastructure\Repository\PluginRepository;
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

use function in_array;

#[AsController]
#[OA\Tag(name: 'Me/Profile - Application Plugin')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
final readonly class ProfileApplicationPluginController
{
    public function __construct(
        private PluginListResourceInterface $pluginListResource,
        private UserApplicationPluginToggleResourceInterface $userApplicationPluginToggleResource,
        private UserTypeIdentification $userTypeIdentification,
        private UserApplicationRepository $userApplicationRepository,
        private PluginRepository $pluginRepository,
    ) {
    }

    #[Route(path: '/v1/profile/user-applications/{userApplicationId}/plugins', methods: [Request::METHOD_GET])]
    #[Route(path: '/v1/me/profile/user-applications/{userApplicationId}/plugins', methods: [Request::METHOD_GET])]
    #[Route(path: '/v1/profile/applications/{userApplicationId}/plugins', methods: [Request::METHOD_GET])]
    #[Route(path: '/v1/me/profile/applications/{userApplicationId}/plugins', methods: [Request::METHOD_GET])]
    #[OA\Get(summary: 'List plugins and activation state for a user application')]
    public function __invoke(string $userApplicationId): JsonResponse
    {
        $userApplication = $this->findUserApplicationOrFail($userApplicationId);
        $this->denyAccessToUserApplication($userApplication);

        return new JsonResponse([
            'items' => array_map(
                static fn (Plugin $plugin): array => $plugin->toArray(),
                $this->pluginListResource->listForUserApplication($userApplication),
            ),
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/profile/user-applications/{userApplicationId}/plugins/{pluginId}', methods: [Request::METHOD_PATCH])]
    #[Route(path: '/v1/me/profile/user-applications/{userApplicationId}/plugins/{pluginId}', methods: [Request::METHOD_PATCH])]
    #[Route(path: '/v1/profile/applications/{userApplicationId}/plugins/{pluginId}', methods: [Request::METHOD_PATCH])]
    #[Route(path: '/v1/me/profile/applications/{userApplicationId}/plugins/{pluginId}', methods: [Request::METHOD_PATCH])]
    #[OA\Patch(summary: 'Toggle plugin activation for a user application')]
    public function patchAction(Request $request, string $userApplicationId, string $pluginId): JsonResponse
    {
        $userApplication = $this->findUserApplicationOrFail($userApplicationId);
        $plugin = $this->findPluginOrFail($pluginId);

        $payload = UserApplicationPluginTogglePayload::fromPayload(JSON::decode((string)$request->getContent(), true));

        return $this->toggle($userApplication, $plugin, $payload->isActive());
    }

    #[Route(path: '/v1/profile/user-applications/{userApplicationId}/plugins/{pluginId}/attach', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/user-applications/{userApplicationId}/plugins/{pluginId}/attach', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/profile/applications/{userApplicationId}/plugins/{pluginId}/attach', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/applications/{userApplicationId}/plugins/{pluginId}/attach', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Attach a plugin to a user application')]
    public function attachAction(string $userApplicationId, string $pluginId): JsonResponse
    {
        $userApplication = $this->findUserApplicationOrFail($userApplicationId);
        $plugin = $this->findPluginOrFail($pluginId);
        $dto = $this->userApplicationPluginToggleResource->attach($userApplication, $plugin);

        return new JsonResponse($dto->toArray(), JsonResponse::HTTP_CREATED);
    }

    #[Route(path: '/v1/profile/user-applications/{userApplicationId}/plugins/{pluginId}/detach', methods: [Request::METHOD_DELETE])]
    #[Route(path: '/v1/me/profile/user-applications/{userApplicationId}/plugins/{pluginId}/detach', methods: [Request::METHOD_DELETE])]
    #[Route(path: '/v1/profile/applications/{userApplicationId}/plugins/{pluginId}/detach', methods: [Request::METHOD_DELETE])]
    #[Route(path: '/v1/me/profile/applications/{userApplicationId}/plugins/{pluginId}/detach', methods: [Request::METHOD_DELETE])]
    #[OA\Delete(summary: 'Detach a plugin from a user application')]
    public function detachAction(string $userApplicationId, string $pluginId): JsonResponse
    {
        $this->userApplicationPluginToggleResource->detach(
            $this->findUserApplicationOrFail($userApplicationId),
            $this->findPluginOrFail($pluginId),
        );

        return new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route(path: '/v1/profile/user-applications/{userApplicationId}/plugins/{pluginId}/activate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/user-applications/{userApplicationId}/plugins/{pluginId}/activate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/profile/applications/{userApplicationId}/plugins/{pluginId}/activate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/applications/{userApplicationId}/plugins/{pluginId}/activate', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Activate a plugin for a user application')]
    public function activateAction(string $userApplicationId, string $pluginId): JsonResponse
    {
        return $this->toggle(
            $this->findUserApplicationOrFail($userApplicationId),
            $this->findPluginOrFail($pluginId),
            true,
        );
    }

    #[Route(path: '/v1/profile/user-applications/{userApplicationId}/plugins/{pluginId}/deactivate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/user-applications/{userApplicationId}/plugins/{pluginId}/deactivate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/profile/applications/{userApplicationId}/plugins/{pluginId}/deactivate', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/applications/{userApplicationId}/plugins/{pluginId}/deactivate', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Deactivate a plugin for a user application')]
    public function deactivateAction(string $userApplicationId, string $pluginId): JsonResponse
    {
        return $this->toggle(
            $this->findUserApplicationOrFail($userApplicationId),
            $this->findPluginOrFail($pluginId),
            false,
        );
    }

    private function toggle(UserApplication $userApplication, PluginEntity $plugin, bool $active): JsonResponse
    {
        $dto = $this->userApplicationPluginToggleResource->toggle($userApplication, $plugin, $active);

        return new JsonResponse($dto->toArray());
    }

    private function findUserApplicationOrFail(string $id): UserApplication
    {
        $userApplication = $this->userApplicationRepository->find($id);

        if (!$userApplication instanceof UserApplication) {
            throw new NotFoundHttpException('User application not found.');
        }

        return $userApplication;
    }

    private function findPluginOrFail(string $idOrKeyName): PluginEntity
    {
        $plugin = null;

        if (UuidHelper::getType($idOrKeyName) !== null) {
            $plugin = $this->pluginRepository->findAdvanced($idOrKeyName);
        }

        if (!$plugin instanceof PluginEntity) {
            $plugin = $this->pluginRepository->findOneBy(['keyName' => $idOrKeyName]);
        }

        if (!$plugin instanceof PluginEntity) {
            throw new NotFoundHttpException('Plugin not found.');
        }

        return $plugin;
    }

    private function denyAccessToUserApplication(UserApplication $userApplication): void
    {
        $actor = $this->userTypeIdentification->getUser();

        if (!$actor instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        if ($actor->getId() !== $userApplication->getUser()->getId() && !$this->isAdminLike($actor)) {
            throw new AccessDeniedHttpException('You cannot access plugins for another user application.');
        }
    }

    private function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
