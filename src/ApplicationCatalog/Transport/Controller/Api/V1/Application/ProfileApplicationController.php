<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Transport\Controller\Api\V1\Application;

use App\ApplicationCatalog\Application\DTO\Application;
use App\ApplicationCatalog\Application\DTO\UserApplicationCreatePayload;
use App\ApplicationCatalog\Application\DTO\UserApplicationConfigurationCreatePayload;
use App\ApplicationCatalog\Application\DTO\UserApplicationPatchPayload;
use App\ApplicationCatalog\Application\Service\UserApplicationCreateService;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\ApplicationCatalog\Infrastructure\Repository\UserApplicationRepository;
use App\Configuration\Domain\Entity\Configuration;
use App\Configuration\Infrastructure\Repository\ConfigurationRepository;
use App\ApplicationCatalog\Application\DTO\UserApplicationMapper;
use App\ApplicationCatalog\Application\DTO\UserApplicationTogglePayload;
use App\ApplicationCatalog\Application\Resource\Interfaces\ApplicationListResourceInterface;
use App\ApplicationCatalog\Application\Resource\Interfaces\UserApplicationToggleResourceInterface;
use App\ApplicationCatalog\Application\Service\Interfaces\UserApplicationCreateServiceInterface;
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
        private UserApplicationCreateServiceInterface $userApplicationCreateService,
        private UserApplicationCreateService $userApplicationCreateServiceConcrete,
        private UserApplicationRepository $userApplicationRepository,
        private ConfigurationRepository $configurationRepository,
        private UserApplicationMapper $userApplicationMapper,
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


    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/profile/user-applications', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/user-applications', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Create a user application for current user')]
    public function createUserApplicationAction(Request $request): JsonResponse
    {
        $payload = UserApplicationCreatePayload::fromPayload(JSON::decode((string)$request->getContent(), true));
        $application = $this->findApplicationOrFail($payload->getApplicationId());
        $currentUser = $this->getCurrentUserOrDeny();

        $created = $this->userApplicationCreateService->create(
            $currentUser,
            $application,
            $payload->getName(),
            $payload->getLogo(),
            $payload->getDescription(),
            $payload->isPublic(),
        );

        return new JsonResponse(
            $this->userApplicationMapper->mapEntityToDto($created, $currentUser)->toArray(),
            JsonResponse::HTTP_CREATED,
        );
    }


    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/profile/user-applications/{id}', methods: [Request::METHOD_PATCH])]
    #[Route(path: '/v1/me/profile/user-applications/{id}', methods: [Request::METHOD_PATCH])]
    #[OA\Patch(summary: 'Update current user user-application metadata')]
    public function patchUserApplicationAction(Request $request, string $id): JsonResponse
    {
        $payload = UserApplicationPatchPayload::fromPayload(JSON::decode((string)$request->getContent(), true));
        $currentUser = $this->getCurrentUserOrDeny();
        $userApplication = $this->findUserApplicationOrFail($id);
        $this->denyUnlessOwner($userApplication, $currentUser);

        if (is_string($payload->getName()) && trim($payload->getName()) !== '') {
            $name = trim($payload->getName());
            $userApplication
                ->setName($name)
                ->setKeyName($this->userApplicationCreateServiceConcrete->generateUniqueKeyName($name, $userApplication->getId()));
        }

        if (is_string($payload->getLogo())) {
            $userApplication->setLogo($payload->getLogo());
        }

        if (is_string($payload->getDescription())) {
            $userApplication->setDescription($payload->getDescription());
        }

        if (is_bool($payload->isPublic())) {
            $userApplication->setPublic($payload->isPublic());
        }

        $this->userApplicationRepository->save($userApplication);

        return new JsonResponse($this->userApplicationMapper->mapEntityToDto($userApplication, $currentUser)->toArray());
    }

    #[Route(path: '/v1/profile/user-applications/{id}', methods: [Request::METHOD_DELETE])]
    #[Route(path: '/v1/me/profile/user-applications/{id}', methods: [Request::METHOD_DELETE])]
    #[OA\Delete(summary: 'Delete current user user-application')]
    public function deleteUserApplicationAction(string $id): JsonResponse
    {
        $currentUser = $this->getCurrentUserOrDeny();
        $userApplication = $this->findUserApplicationOrFail($id);
        $this->denyUnlessOwner($userApplication, $currentUser);

        $this->userApplicationRepository->remove($userApplication);

        return new JsonResponse(status: JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/profile/user-applications/{id}/configurations', methods: [Request::METHOD_POST])]
    #[Route(path: '/v1/me/profile/user-applications/{id}/configurations', methods: [Request::METHOD_POST])]
    #[OA\Post(summary: 'Create a configuration for current user user-application')]
    public function createUserApplicationConfigurationAction(Request $request, string $id): JsonResponse
    {
        $payload = UserApplicationConfigurationCreatePayload::fromPayload(JSON::decode((string)$request->getContent(), true));
        $currentUser = $this->getCurrentUserOrDeny();
        $userApplication = $this->findUserApplicationOrFail($id);
        $this->denyUnlessOwner($userApplication, $currentUser);

        $configuration = (new Configuration())
            ->setCode($payload->getCode())
            ->setKeyName($payload->getKeyName())
            ->setValue($payload->getValue())
            ->setStatus($payload->getStatus())
            ->setUserApplication($userApplication);

        $this->configurationRepository->save($configuration);

        return new JsonResponse([
            'id' => $configuration->getId(),
            'code' => $configuration->getCode(),
            'keyName' => $configuration->getKeyName(),
            'value' => $configuration->getValue(),
            'status' => $configuration->getStatus(),
            'userApplicationId' => $userApplication->getId(),
        ], JsonResponse::HTTP_CREATED);
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
        $application = $this->applicationRepository->findOneById($id);

        if (!$application instanceof ApplicationEntity) {
            throw new NotFoundHttpException('Application not found.');
        }

        return $application;
    }


    private function findUserApplicationOrFail(string $id): UserApplication
    {
        $userApplication = $this->userApplicationRepository->find($id);

        if (!$userApplication instanceof UserApplication) {
            throw new NotFoundHttpException('User application not found.');
        }

        return $userApplication;
    }

    private function denyUnlessOwner(UserApplication $userApplication, User $currentUser): void
    {
        if ($userApplication->getUser()->getId() !== $currentUser->getId()) {
            throw new AccessDeniedHttpException('Only owner can modify this user application.');
        }
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
