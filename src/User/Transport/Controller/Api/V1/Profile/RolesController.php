<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for RolesController endpoints.
 *
 * @package App\User\Transport\Controller\Api\V1\Profile
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[OA\Tag(name: 'Me - Profile')]
class RolesController
{
    public function __construct(
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Get current user roles as an array, accessible only for 'IS_AUTHENTICATED_FULLY' users.
     */
    #[Route(
        path: '/v1/profile/roles',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(
        summary: 'Lister les rôles du profil courant',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Périmètre des données: uniquement les rôles hérités du compte authentifié.',
        security: [['Bearer' => []], ['ApiKey' => []]],
    )]
    #[OA\Response(
        response: 200,
        description: 'Logged in user roles',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(type: 'string', example: 'ROLE_USER'),
            example: ['ROLE_USER', 'ROLE_LOGGED'],
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function __invoke(User $loggedInUser): JsonResponse
    {
        return new JsonResponse($this->rolesService->getInheritedRoles($loggedInUser->getRoles()));
    }
}
