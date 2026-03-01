<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\Address;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserAvatar;
use App\User\Domain\Enum\AddressType;
use JsonException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API controller for IndexController endpoints.
 *
 * @package App\User\Transport\Controller\Api\V1\Profile
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[OA\Tag(name: 'Me - Profile')]
class IndexController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RolesServiceInterface $rolesService,
        private readonly UserResource $userResource,
    ) {
    }

    /**
     * Get current user profile data, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     *
     * @throws JsonException
     */
    #[Route(
        path: '/v1/profile',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(
        summary: 'Lire le profil courant',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Périmètre des données: profil complet de l’utilisateur authentifié uniquement.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'User profile data',
        content: new JsonContent(
            ref: new Model(
                type: User::class,
                groups: ['set.UserProfile'],
            ),
            type: 'object',
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function __invoke(User $loggedInUser): JsonResponse
    {
        return $this->createProfileResponse($loggedInUser);
    }

    /**
     * @throws JsonException
     */
    #[Route(
        path: '/v1/profile',
        methods: [Request::METHOD_PATCH],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Patch(
        summary: 'Mettre à jour partiellement le profil courant',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Périmètre des données: uniquement les champs du profil de l’utilisateur authentifié.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\RequestBody(
        required: true,
        description: 'Exemple de payload de patch profil',
        content: new JsonContent(
            type: 'object',
            example: [
                'firstName' => 'Ada',
                'lastName' => 'Lovelace',
                'timezone' => 'Europe/Paris',
                'userProfile' => [
                    'bio' => 'Senior Backend Engineer',
                    'phone' => '+33102030405',
                ],
            ],
        ),
    )]
    #[OA\Response(response: 200, description: 'Profil mis à jour')]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function patchProfileAction(Request $request, User $loggedInUser): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);
        /** @var array<string, mixed> $profilePayload */
        $profilePayload = isset($payload['userProfile']) && is_array($payload['userProfile']) ? $payload['userProfile'] : $payload;

        if (isset($payload['username']) && is_string($payload['username'])) {
            $loggedInUser->setUsername($payload['username']);
        }
        if (isset($payload['firstName']) && is_string($payload['firstName'])) {
            $loggedInUser->setFirstName($payload['firstName']);
        }
        if (isset($payload['lastName']) && is_string($payload['lastName'])) {
            $loggedInUser->setLastName($payload['lastName']);
        }
        if (isset($payload['email']) && is_string($payload['email'])) {
            $loggedInUser->setEmail($payload['email']);
        }
        if (isset($payload['timezone']) && is_string($payload['timezone'])) {
            $loggedInUser->setTimezone($payload['timezone']);
        }
        if (isset($payload['language']) && is_string($payload['language'])) {
            $loggedInUser->setLanguage(Language::from($payload['language']));
        }
        if (isset($payload['locale']) && is_string($payload['locale'])) {
            $loggedInUser->setLocale(Locale::from($payload['locale']));
        }

        $profile = $loggedInUser->getOrCreateUserProfile();
        if (array_key_exists('phone', $profilePayload)) {
            $profile->setPhone(is_string($profilePayload['phone']) ? $profilePayload['phone'] : null);
        }
        if (array_key_exists('bio', $profilePayload)) {
            $profile->setBio(is_string($profilePayload['bio']) ? $profilePayload['bio'] : null);
        }
        if (array_key_exists('contacts', $profilePayload) && (is_array($profilePayload['contacts']) || $profilePayload['contacts'] === null)) {
            $profile->setContacts($profilePayload['contacts']);
        }
        if (isset($profilePayload['birthDate']) && is_string($profilePayload['birthDate']) && $profilePayload['birthDate'] !== '') {
            $profile->setBirthDate(new \DateTimeImmutable($profilePayload['birthDate']));
        }

        $this->userResource->save($loggedInUser, true);

        return $this->createProfileResponse($loggedInUser);
    }

    /**
     * @throws JsonException
     */
    #[Route(
        path: '/v1/profile/avatar',
        methods: [Request::METHOD_PUT],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function updateAvatarAction(Request $request, User $loggedInUser): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        if (!isset($payload['url']) || !is_string($payload['url']) || $payload['url'] === '') {
            return new JsonResponse(['message' => 'Field "url" is required.'], Response::HTTP_BAD_REQUEST);
        }

        $profile = $loggedInUser->getOrCreateUserProfile();
        $avatar = $profile->getAvatar() ?? new UserAvatar($profile);
        $avatar->setUrl($payload['url']);

        if (array_key_exists('mediaId', $payload)) {
            $avatar->setMediaId(is_string($payload['mediaId']) ? $payload['mediaId'] : null);
        }

        $profile->setAvatar($avatar);
        $this->userResource->save($loggedInUser, true);

        return $this->createProfileResponse($loggedInUser);
    }

    /**
     * @throws JsonException
     */
    #[Route(
        path: '/v1/profile/address',
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function addAddressAction(Request $request, User $loggedInUser): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = JSON::decode($request->getContent() ?: '{}', true);

        foreach (['streetLine1', 'postalCode', 'city', 'countryCode'] as $requiredField) {
            if (!isset($payload[$requiredField]) || !is_string($payload[$requiredField]) || $payload[$requiredField] === '') {
                return new JsonResponse(['message' => sprintf('Field "%s" is required.', $requiredField)], Response::HTTP_BAD_REQUEST);
            }
        }

        $address = (new Address())
            ->setStreetLine1($payload['streetLine1'])
            ->setPostalCode($payload['postalCode'])
            ->setCity($payload['city'])
            ->setCountryCode($payload['countryCode']);

        if (isset($payload['type']) && is_string($payload['type']) && $payload['type'] !== '') {
            $address->setType(AddressType::from($payload['type']));
        }
        if (array_key_exists('streetLine2', $payload)) {
            $address->setStreetLine2(is_string($payload['streetLine2']) ? $payload['streetLine2'] : null);
        }
        if (array_key_exists('region', $payload)) {
            $address->setRegion(is_string($payload['region']) ? $payload['region'] : null);
        }

        $loggedInUser->getOrCreateUserProfile()->addAddress($address);
        $this->userResource->save($loggedInUser, true);

        return $this->createProfileResponse($loggedInUser);
    }

    /**
     * @throws JsonException
     */
    private function createProfileResponse(User $loggedInUser): JsonResponse
    {
        /** @var array<string, string|array<string, string>> $output */
        $output = JSON::decode(
            $this->serializer->serialize(
                $loggedInUser,
                'json',
                [
                    'groups' => User::SET_USER_PROFILE,
                ]
            ),
            true,
        );
        /** @var array<int, string> $roles */
        $roles = $output['roles'];
        $output['roles'] = $this->rolesService->getInheritedRoles($roles);

        return new JsonResponse($output);
    }
}
