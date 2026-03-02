<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

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
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
#[OA\Tag(name: 'Me - Profile')]
class ConfigurationsController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly ConfigurationResourceInterface $configurationResource,
    ) {
    }

    /**
     * @throws JsonException
     */
    #[Route(
        path: '/v1/me/profile/configurations',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(
        summary: 'Lister les configurations liées au profil courant',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Retourne les configurations rattachées au profil de l’utilisateur authentifié. Filtre optionnel par keyName (recherche partielle insensible à la casse).',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $currentUser = $this->getCurrentUserOrDeny();
        $profile = $currentUser->getOrCreateUserProfile();

        $items = $this->configurationResource->findByProfileAndKeyName(
            $profile,
            $request->query->getString('keyName') ?: null,
        );

        return $this->jsonResponse($items);
    }

    /**
     * @throws JsonException
     */
    #[Route(
        path: '/v1/me/profile/configurations',
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Post(summary: 'Ajouter une configuration au profil courant', security: [['Bearer' => []], ['ApiKey' => []]])]
    public function createAction(Request $request): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();
        $payload = $this->decodePayload($request);

        $configuration = (new Configuration())
            ->setProfile($user->getOrCreateUserProfile());

        $this->applyPayload($configuration, $payload, true);
        $this->configurationResource->save($configuration, true);

        return $this->jsonResponse($configuration, Response::HTTP_CREATED);
    }

    /**
     * @throws JsonException
     */
    #[Route(
        path: '/v1/me/profile/configurations/{configurationId}',
        methods: [Request::METHOD_PUT],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Put(summary: 'Mettre à jour une configuration du profil courant', security: [['Bearer' => []], ['ApiKey' => []]])]
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
     */
    #[Route(
        path: '/v1/me/profile/configurations/{configurationId}',
        methods: [Request::METHOD_PATCH],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Patch(summary: 'Mettre à jour partiellement une configuration du profil courant', security: [['Bearer' => []], ['ApiKey' => []]])]
    public function patchAction(Request $request, string $configurationId): JsonResponse
    {
        $user = $this->getCurrentUserOrDeny();
        $payload = $this->decodePayload($request);

        $configuration = $this->getOwnedConfigurationOr404($configurationId, $user);
        $this->applyPayload($configuration, $payload, false);
        $this->configurationResource->save($configuration, true);

        return $this->jsonResponse($configuration);
    }

    #[Route(
        path: '/v1/me/profile/configurations/{configurationId}',
        methods: [Request::METHOD_DELETE],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Delete(summary: 'Supprimer une configuration du profil courant', security: [['Bearer' => []], ['ApiKey' => []]])]
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

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function decodePayload(Request $request): array
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
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

        $profile = $configuration->getProfile();
        if ($profile === null || $profile->getId() !== $user->getOrCreateUserProfile()->getId()) {
            throw new NotFoundHttpException('Configuration not found for current user.');
        }

        return $configuration;
    }

    private function jsonResponse(mixed $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize(
                $data,
                'json',
                ['groups' => ['Configuration.show']],
            ),
            $status,
            json: true,
        );
    }
}
