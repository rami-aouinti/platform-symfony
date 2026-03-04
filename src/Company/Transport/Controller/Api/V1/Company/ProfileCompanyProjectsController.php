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
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for profile company projects endpoints.
 *
 * @method ProjectResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/me/profile/companies')]
#[IsGranted('ROLE_LOGGED')]
#[OA\Tag(name: 'Me/Profile - Profile')]
class ProfileCompanyProjectsController extends Controller
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
