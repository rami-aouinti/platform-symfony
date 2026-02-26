<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\Task\Application\Resource\Interfaces\ProjectResourceInterface;
use App\Task\Application\Resource\ProjectResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method ProjectResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/companies')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Company Management')]
class CompanyProjectsController extends Controller
{
    public function __construct(ProjectResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/projects', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function projectsAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->find(criteria: [
                'company' => $id,
            ]),
            $this->getResource(),
        );
    }
}
