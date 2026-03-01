<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\SocialAccount;
use App\User\Domain\Entity\User;
use App\User\Domain\Enum\SocialProvider;
use App\User\Infrastructure\Repository\SocialAccountRepository;
use JsonException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[OA\Tag(name: 'Me - Profile')]
class SocialAccountsController
{
    public function __construct(
        private readonly SocialAccountRepository $socialAccountRepository,
        private readonly UserResource $userResource,
    ) {
    }

    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/me/social-accounts', methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function listAction(User $loggedInUser): JsonResponse
    {
        $accounts = $this->socialAccountRepository->findBy(['user' => $loggedInUser]);

        return new JsonResponse([
            'items' => array_map(
                static fn (SocialAccount $account): array => [
                    'provider' => $account->getProvider()->value,
                    'providerUserId' => $account->getProviderUserId(),
                    'providerEmail' => $account->getProviderEmail(),
                ],
                $accounts,
            ),
        ]);
    }

    /**
     * @throws JsonException
     */
    #[Route(path: '/v1/me/social-accounts/link', methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function linkAction(Request $request, User $loggedInUser): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        if (!isset($payload['provider']) || !is_string($payload['provider']) || $payload['provider'] === '') {
            return new JsonResponse(['message' => 'Field "provider" is required.'], Response::HTTP_BAD_REQUEST);
        }
        if (!isset($payload['providerUserId']) || !is_string($payload['providerUserId']) || $payload['providerUserId'] === '') {
            return new JsonResponse(['message' => 'Field "providerUserId" is required.'], Response::HTTP_BAD_REQUEST);
        }

        $provider = SocialProvider::tryFrom($payload['provider']);
        if (!$provider instanceof SocialProvider) {
            return new JsonResponse(['message' => 'Unsupported social provider.'], Response::HTTP_BAD_REQUEST);
        }

        /** @var SocialAccount|null $existing */
        $existing = $this->socialAccountRepository->findOneBy(['user' => $loggedInUser, 'provider' => $provider]);
        if ($existing instanceof SocialAccount) {
            $existing->setProviderUserId($payload['providerUserId']);
            $existing->setProviderEmail(isset($payload['providerEmail']) && is_string($payload['providerEmail']) ? $payload['providerEmail'] : null);
        } else {
            $account = new SocialAccount($loggedInUser, $provider, $payload['providerUserId']);
            $account->setProviderEmail(isset($payload['providerEmail']) && is_string($payload['providerEmail']) ? $payload['providerEmail'] : null);
            $loggedInUser->addSocialAccount($account);
        }

        $this->userResource->save($loggedInUser, true);

        return $this->listAction($loggedInUser);
    }

    #[Route(path: '/v1/me/social-accounts/{provider}', methods: [Request::METHOD_DELETE])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function unlinkAction(User $loggedInUser, string $provider): JsonResponse
    {
        $providerEnum = SocialProvider::tryFrom($provider);
        if (!$providerEnum instanceof SocialProvider) {
            return new JsonResponse(['message' => 'Unsupported social provider.'], Response::HTTP_BAD_REQUEST);
        }

        /** @var SocialAccount|null $account */
        $account = $this->socialAccountRepository->findOneBy(['user' => $loggedInUser, 'provider' => $providerEnum]);

        if (!$account instanceof SocialAccount) {
            return new JsonResponse(['message' => 'Social account not found for current user.'], Response::HTTP_NOT_FOUND);
        }

        $loggedInUser->removeSocialAccount($account);
        $this->userResource->save($loggedInUser, true);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
