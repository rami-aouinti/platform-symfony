<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\Task\Application\Resource\Interfaces\ProjectResourceInterface;
use App\Task\Application\Resource\ProjectResource;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for CompanyProjectsController endpoints.
 *
 * @method ProjectResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Company\Transport\Controller\Api\V1\Company
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/companies')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Admin - Company Management')]
class CompanyProjectsController extends Controller
{
    public function __construct(
        ProjectResourceInterface $resource,
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/projects', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    #[OA\Get(
        summary: 'Lister les projets d\'une company accessible',
        description: 'Audience cible: utilisateur authentifié (ROLE_LOGGED / IS_AUTHENTICATED_FULLY). Autorisé si l\'utilisateur courant est owner de la company ou membre actif de cette company. Retourne 403 sinon.',
        security: [[
            'Bearer' => [],
        ], [
            'ApiKey' => [],
        ]],
    )]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'string', format: 'uuid'),
        description: 'Identifiant de la company cible.',
    )]
    #[OA\Response(response: 200, description: 'Liste des projets de la company.')]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    public function projectsAction(Request $request, string $id): Response
    {
        $currentUser = $this->getCurrentUserOrDeny();

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findProjectsForMyCompanyAccess($id, $currentUser),
            $this->getResource(),
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
