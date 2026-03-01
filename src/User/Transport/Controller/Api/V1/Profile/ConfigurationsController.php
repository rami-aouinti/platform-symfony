<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\Configuration\Application\Resource\Interfaces\ConfigurationResourceInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
    #[OA\Parameter(name: 'keyName', in: 'query', required: false, description: 'Filtre partiel sur keyName (contains, case-insensitive).', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(
        response: 200,
        description: 'List of profile configurations',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                type: 'object',
                properties: [
                    new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'code', type: 'string'),
                    new OA\Property(property: 'keyName', type: 'string'),
                    new OA\Property(property: 'value', type: 'object', additionalProperties: true),
                    new OA\Property(property: 'status', type: 'string'),
                ],
            ),
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function __invoke(Request $request): JsonResponse
    {
        $currentUser = $this->getCurrentUserOrDeny();
        $profile = $currentUser->getOrCreateUserProfile();

        $items = $this->configurationResource->findByProfileAndKeyName(
            $profile,
            $request->query->getString('keyName') ?: null,
        );

        return new JsonResponse(
            $this->serializer->serialize(
                $items,
                'json',
                ['groups' => ['Configuration.show']],
            ),
            json: true,
        );
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
