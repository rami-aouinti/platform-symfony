<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Auth;

use App\General\Domain\Utils\JSON;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\Security\SecurityUser;
use App\User\Domain\Entity\SocialAccount;
use App\User\Domain\Entity\User;
use App\User\Domain\Enum\SocialProvider;
use App\User\Infrastructure\Repository\SocialAccountRepository;
use App\User\Infrastructure\Repository\UserRepository;
use JsonException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

use function is_array;
use function is_string;

/**
 * @package App\User
 */
#[AsController]
#[OA\Tag(name: 'Authentication')]
class SocialConnectController
{
    public function __construct(
        private readonly SocialAccountRepository $socialAccountRepository,
        private readonly UserRepository $userRepository,
        private readonly JWTTokenManagerInterface $jwtTokenManager,
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Throwable
     */
    #[Route(
        path: '/v1/auth/social/connect',
        methods: [Request::METHOD_POST],
    )]
    #[OA\Post(
        security: [],
        requestBody: new OA\RequestBody(
            required: true,
            content: new JsonContent(
                properties: [
                    new Property(property: 'provider', type: 'string', example: 'google'),
                    new Property(property: 'providerUserId', type: 'string', example: '123456789'),
                    new Property(property: 'email', type: 'string', example: 'john.doe@test.com'),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'JSON Web Token for user',
                content: new JsonContent(
                    properties: [
                        new Property(property: 'token', type: 'string'),
                        new Property(property: 'provider', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $payload = JSON::decode($request->getContent(), true);

        if (!is_array($payload)) {
            throw new BadRequestHttpException('Invalid request body.');
        }

        $provider = SocialProvider::tryFrom((string)($payload['provider'] ?? ''));
        $providerUserId = $payload['providerUserId'] ?? null;
        $email = $payload['email'] ?? null;

        if (!$provider instanceof SocialProvider || !is_string($providerUserId) || $providerUserId === '' || !is_string($email) || $email === '') {
            throw new BadRequestHttpException('provider, providerUserId and email are required.');
        }

        $account = $this->socialAccountRepository->findOneByProviderAndExternalId($provider, $providerUserId);

        if (!$account instanceof SocialAccount) {
            /** @var User|null $user */
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user instanceof User) {
                throw new BadRequestHttpException('No local user found for specified social account/email.');
            }

            $account = (new SocialAccount($user, $provider, $providerUserId))
                ->setProviderEmail($email);

            $user->addSocialAccount($account);
            $this->userRepository->save($user, true);
        }

        $user = $account->getUser();
        $securityUser = new SecurityUser($user, $this->rolesService->getInheritedRoles($user->getRoles()));

        return new JsonResponse([
            'token' => $this->jwtTokenManager->create($securityUser),
            'provider' => $provider->value,
        ], Response::HTTP_OK);
    }
}
