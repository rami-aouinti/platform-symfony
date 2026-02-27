<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\Company\Application\Resource\CompanyMembershipResource;
use App\Company\Application\Resource\Interfaces\CompanyMembershipResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for CompanyMembersController endpoints.
 *
 * @method CompanyMembershipResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Company\Transport\Controller\Api\V1\Company
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/companies')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Company Management')]
class CompanyMembersController extends Controller
{
    public function __construct(CompanyMembershipResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/members', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function membersAction(Request $request, string $id): Response
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
