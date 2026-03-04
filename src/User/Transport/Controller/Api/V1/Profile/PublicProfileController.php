<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use JsonException;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

#[AsController]
#[OA\Tag(name: 'Public - User Profile')]
class PublicProfileController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly RolesServiceInterface $rolesService,
        private readonly UserResource $userResource,
    ) {
    }

    /**
     * @throws JsonException
     * @throws Throwable
     */
    #[Route(
        path: '/v1/user/{userId}/profile',
        requirements: [
            'userId' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_GET],
    )]
    #[OA\Get(
        summary: 'Lire le profil public d\'un utilisateur',
        description: 'Endpoint public permettant de lire le profil d\'un utilisateur via son identifiant.',
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
    public function __invoke(string $userId): JsonResponse
    {
        /** @var User $user */
        $user = $this->userResource->findOne($userId, true);

        /** @var array<string, string|array<string, string>> $output */
        $output = JSON::decode(
            $this->serializer->serialize(
                $user,
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
