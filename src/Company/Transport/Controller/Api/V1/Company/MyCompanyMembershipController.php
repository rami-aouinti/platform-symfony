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
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method CompanyMembershipResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/api/v1/me/companies')]
#[IsGranted('ROLE_LOGGED')]
#[OA\Tag(name: 'Company Management')]
class MyCompanyMembershipController extends Controller
{
    public function __construct(CompanyMembershipResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function companiesAction(Request $request): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findMyCompanies(),
            $this->getResource(),
        );
    }

    #[Route(path: '/{companyId}/membership', requirements: [
        'companyId' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function membershipAction(Request $request, string $companyId): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findMyMembership($companyId),
            $this->getResource(),
        );
    }
}
