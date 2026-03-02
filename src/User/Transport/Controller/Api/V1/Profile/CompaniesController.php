<?php

declare(strict_types=1);

namespace App\User\Transport\Controller\Api\V1\Profile;

use App\Company\Application\Resource\CompanyMembershipResource;
use App\Company\Domain\Entity\Company;
use App\Task\Application\Resource\ProjectResource;
use App\Task\Domain\Entity\Project;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * API controller for profile companies and projects endpoints.
 *
 * @package App\User\Transport\Controller\Api\V1\Profile
 */
#[AsController]
#[OA\Tag(name: 'Me/Profile - Profile')]
class CompaniesController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly CompanyMembershipResource $companyMembershipResource,
        private readonly ProjectResource $projectResource,
    ) {
    }

    #[Route(
        path: '/v1/me/profile/companies',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(
        summary: 'Lister les companies du profil courant',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Retourne les companies liées à l’utilisateur authentifié via ses memberships.',
        security: [[
            'Bearer' => [],
        ], [
            'ApiKey' => [],
        ]],
    )]
    #[OA\Response(
        response: 200,
        description: 'List of logged in user companies',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: Company::class,
                    groups: ['Company.id', 'Company.legalName', 'Company.status', 'Company.photoUrl'],
                ),
            ),
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function companiesAction(): JsonResponse
    {
        $this->getCurrentUserOrDeny();

        $memberships = $this->companyMembershipResource->findMyCompanies();
        $companies = [];

        foreach ($memberships as $membership) {
            $company = $membership->getCompany();
            $companies[$company->getId()] = $company;
        }

        return new JsonResponse(
            $this->serializer->serialize(
                array_values($companies),
                'json',
                [
                    'groups' => ['Company.id', 'Company.legalName', 'Company.status', 'Company.photoUrl'],
                ],
            ),
            json: true,
        );
    }

    #[Route(
        path: '/v1/me/profile/projects',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Get(
        summary: 'Lister les projets du profil courant',
        description: 'Audience cible: utilisateurs connectés. Rôle minimal: IS_AUTHENTICATED_FULLY. Retourne les projets possédés par l’utilisateur authentifié (et donc accessibles côté profil).',
        security: [[
            'Bearer' => [],
        ], [
            'ApiKey' => [],
        ]],
    )]
    #[OA\Response(
        response: 200,
        description: 'List of logged in user projects',
        content: new JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(
                    type: Project::class,
                    groups: ['Project.id', 'Project.name', 'Project.description', 'Project.status', 'Project.photoUrl'],
                ),
            ),
        ),
    )]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function projectsAction(): JsonResponse
    {
        $currentUser = $this->getCurrentUserOrDeny();

        $projects = $this->projectResource->find(
            criteria: [
                'owner' => $currentUser,
            ],
            orderBy: [
                'name' => 'ASC',
            ],
        );

        return new JsonResponse(
            $this->serializer->serialize(
                $projects,
                'json',
                [
                    'groups' => ['Project.id', 'Project.name', 'Project.description', 'Project.status', 'Project.photoUrl'],
                ],
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
