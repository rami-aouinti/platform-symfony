<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\Task\Application\Resource\Interfaces\SprintResourceInterface;
use App\Task\Application\Resource\SprintResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function filter_var;

/**
 * API controller for CompanySprintsController endpoints.
 *
 * @method SprintResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Company\Transport\Controller\Api\V1\Company
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/companies')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Company Management')]
class CompanySprintsController extends Controller
{
    public function __construct(SprintResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/sprints', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function sprintsAction(Request $request, string $id): Response
    {
        $active = $this->resolveActiveFilter($request);

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findByCompany($id, $active),
            $this->getResource(),
        );
    }

    private function resolveActiveFilter(Request $request): ?bool
    {
        $active = $request->query->get('active');

        if (!is_string($active) || $active === '') {
            return null;
        }

        return filter_var($active, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }
}
