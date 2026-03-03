<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\ApplicationCatalog\Infrastructure\Repository\ApplicationRepository;
use App\Configuration\Application\Resource\Interfaces\ConfigurationResourceInterface;
use App\Configuration\Domain\Entity\Configuration;
use App\General\Domain\Utils\JSON;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use JsonException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

use function array_key_exists;
use function in_array;
use function is_array;
use function is_scalar;
use function is_string;
use function sprintf;
use function trim;

#[AsController]
#[OA\Tag(name: 'Me/Profile - Profile')]
readonly class ConfigurationsController
{
    public function __construct(
        private SerializerInterface $serializer,
        private UserTypeIdentification $userTypeIdentification,
        private ConfigurationResourceInterface $configurationResource,
        private ApplicationRepository $applicationRepository,
    ) {
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     */
    #[Route(path: '/v1/me/profile/configurations', methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function __invoke(Request $request): JsonResponse
    {
        $currentUser = $this->getCurrentUserOrDeny();
        $userApplication = $this->resolveUserApplicationFromRequest($request, $currentUser);

        $items = $this->configurationResource->findByUserApplicationAndKeyName(
            $userApplication,
            $this->resolveKeyNameFilter($request),
        );

        return $this->jsonResponse($items);
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     * @throws Throwable
     */
    #[Route(path: '/v1/me/profile/configurations', methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function createAction(Request $request): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();
        $payload = $this->decodePayload($request);
        $userApplication = $this->resolveUserApplicationFromPayload($payload, $user);

        if (!array_key_exists('keyName', $payload) || !is_string($payload['keyName']) || $payload['keyName'] === '') {
            throw new BadRequestHttpException('Field "keyName" is required and must be a non-empty string.');
        }

        $existing = $this->configurationResource->findOneByUserApplicationAndKeyName($userApplication, $payload['keyName']);

        if ($existing instanceof Configuration) {
            $this->applyPayload($existing, $payload, false);
            $existing->setUserApplication($userApplication);
            $this->configurationResource->save($existing, true);

            return $this->jsonResponse($existing);
        }

        $configuration = new Configuration();
        $configuration->setUserApplication($userApplication);

        $this->applyPayload($configuration, $payload, true);
        $this->configurationResource->save($configuration, true);

        return $this->jsonResponse($configuration, Response::HTTP_CREATED);
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     * @throws Throwable
     */
    #[Route(path: '/v1/me/profile/configurations/{configurationId}', requirements: ['configurationId' => Requirement::UUID_V1], methods: [Request::METHOD_PUT])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function updateAction(Request $request, string $configurationId): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();
        $payload = $this->decodePayload($request);

        $configuration = $this->getOwnedConfigurationOr404($configurationId, $user);
        $this->applyPayload($configuration, $payload, true);
        $this->configurationResource->save($configuration, true);

        return $this->jsonResponse($configuration);
    }

    /**
     * @throws JsonException
     * @throws ExceptionInterface
     * @throws Throwable
     */
    #[Route(path: '/v1/me/profile/configurations/{configurationId}', requirements: ['configurationId' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function patchAction(Request $request, string $configurationId): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();
        $payload = $this->decodePayload($request);

        $configuration = $this->getOwnedConfigurationOr404($configurationId, $user);
        $this->applyPayload($configuration, $payload, false);
        $this->configurationResource->save($configuration, true);

        return $this->jsonResponse($configuration);
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/v1/me/profile/configurations/{configurationId}', requirements: ['configurationId' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function deleteAction(string $configurationId): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();

        $configuration = $this->getOwnedConfigurationOr404($configurationId, $user);
        $this->configurationResource->delete($configuration->getId(), true);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function getCurrentUserOrDeny(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }

    /** @return array<string, mixed> */
    private function decodePayload(Request $request): array
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        return $payload;
    }

    private function resolveKeyNameFilter(Request $request): ?string
    {
        $keyName = $request->query->get('keyName');

        if (!is_scalar($keyName)) {
            return null;
        }

        $normalizedKeyName = trim((string)$keyName);

        return $normalizedKeyName !== '' ? $normalizedKeyName : null;
    }

    /** @param array<string, mixed> $payload */
    private function applyPayload(Configuration $configuration, array $payload, bool $strict): void
    {
        if ($strict) {
            foreach (['code', 'keyName', 'value'] as $requiredField) {
                if (!array_key_exists($requiredField, $payload)) {
                    throw new BadRequestHttpException(sprintf('Field "%s" is required.', $requiredField));
                }
            }
        }

        if (array_key_exists('code', $payload)) {
            if (!is_string($payload['code']) || $payload['code'] === '') {
                throw new BadRequestHttpException('Field "code" must be a non-empty string.');
            }
            $configuration->setCode($payload['code']);
        }

        if (array_key_exists('keyName', $payload)) {
            if (!is_string($payload['keyName']) || $payload['keyName'] === '') {
                throw new BadRequestHttpException('Field "keyName" must be a non-empty string.');
            }
            $configuration->setKeyName($payload['keyName']);
        }

        if (array_key_exists('value', $payload)) {
            if (!is_array($payload['value'])) {
                throw new BadRequestHttpException('Field "value" must be an object/array.');
            }
            $configuration->setValue($payload['value']);
        }

        if (array_key_exists('status', $payload)) {
            if (!is_string($payload['status']) || $payload['status'] === '') {
                throw new BadRequestHttpException('Field "status" must be a non-empty string.');
            }
            $configuration->setStatus($payload['status']);
        }
    }

    private function getOwnedConfigurationOr404(string $configurationId, User $user): Configuration
    {
        $configuration = $this->configurationResource->findOne($configurationId, false);

        if (!$configuration instanceof Configuration) {
            throw new NotFoundHttpException('Configuration not found for current user.');
        }

        $userApplication = $configuration->getUserApplication();
        if (
            !$userApplication instanceof UserApplication
            || (!$this->isAdminOrRoot($user) && $userApplication->getUser()->getId() !== $user->getId())
        ) {
            throw new NotFoundHttpException('Configuration not found for current user.');
        }

        return $configuration;
    }

    private function resolveUserApplicationFromRequest(Request $request, User $currentUser): UserApplication
    {
        $applicationId = $request->query->get('applicationId');
        $userApplicationId = $request->query->get('userApplicationId');

        return $this->resolveUserApplication(
            is_scalar($applicationId) ? trim((string)$applicationId) : null,
            is_scalar($userApplicationId) ? trim((string)$userApplicationId) : null,
            $currentUser,
        );
    }

    /** @param array<string, mixed> $payload */
    private function resolveUserApplicationFromPayload(array $payload, User $currentUser): UserApplication
    {
        $applicationId = $payload['applicationId'] ?? null;
        $userApplicationId = $payload['userApplicationId'] ?? null;

        return $this->resolveUserApplication(
            is_scalar($applicationId) ? trim((string)$applicationId) : null,
            is_scalar($userApplicationId) ? trim((string)$userApplicationId) : null,
            $currentUser,
        );
    }

    private function resolveUserApplication(?string $applicationId, ?string $userApplicationId, User $currentUser): UserApplication
    {
        if ($applicationId === null && $userApplicationId === null) {
            throw new BadRequestHttpException('Either "applicationId" or "userApplicationId" must be provided.');
        }

        if (is_string($userApplicationId) && $userApplicationId !== '') {
            foreach ($currentUser->getUserApplications() as $userApplication) {
                if ($userApplication->getId() === $userApplicationId) {
                    if (!$userApplication->isActive()) {
                        throw new AccessDeniedHttpException('Application is not active for current user.');
                    }

                    return $userApplication;
                }
            }

            throw new NotFoundHttpException('User application not found for current user.');
        }

        if (!is_string($applicationId) || $applicationId === '') {
            throw new BadRequestHttpException('Field "applicationId" must be a non-empty string when provided.');
        }

        $application = $this->applicationRepository->find($applicationId);

        if (!$application instanceof Application) {
            throw new NotFoundHttpException('Application not found.');
        }

        foreach ($currentUser->getUserApplications() as $userApplication) {
            if ($userApplication->getApplication()->getId() === $application->getId()) {
                if (!$userApplication->isActive()) {
                    throw new AccessDeniedHttpException('Application is not active for current user.');
                }

                return $userApplication;
            }
        }

        throw new NotFoundHttpException('Application not enabled for current user.');
    }

    private function isAdminOrRoot(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    /**
     * @throws ExceptionInterface
     */
    private function jsonResponse(mixed $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($data, 'json', ['groups' => ['Configuration.show']]),
            $status,
            json: true,
        );
    }
}
