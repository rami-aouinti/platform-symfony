<?php

declare(strict_types=1);

namespace App\Company\Transport\Controller\Api\V1\Company;

use App\Company\Application\Resource\CompanyMembershipResource;
use App\Company\Application\Resource\Interfaces\CompanyMembershipResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
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
#[Route(path: '/api/v1/admin/companies')]
#[Route(path: '/v1/companies')]
#[IsGranted('ROLE_LOGGED')]
#[OA\Tag(name: 'Company Management')]
class CompanyMembersController extends Controller
{
    public function __construct(CompanyMembershipResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{companyId}/members', requirements: [
        'companyId' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function membersAction(Request $request, string $companyId): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findByCompany($companyId),
            $this->getResource(),
        );
    }

    #[Route(path: '/{companyId}/memberships', requirements: [
        'companyId' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function membershipsAction(Request $request, string $companyId): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findByCompany($companyId),
            $this->getResource(),
        );
    }

    #[Route(path: '/{companyId}/members', requirements: [
        'companyId' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_POST])]
    public function inviteOrAttachAction(Request $request, string $companyId): Response
    {
        $payload = $request->toArray();
        $membership = $this->getResource()->inviteOrAttach(
            $companyId,
            (string)($payload['userId'] ?? ''),
            isset($payload['role']) ? (string)$payload['role'] : null,
            isset($payload['status']) ? (string)$payload['status'] : null,
        );

        return $this->getResponseHandler()->createResponse(
            $request,
            $membership,
            $this->getResource(),
            Response::HTTP_CREATED,
        );
    }

    #[Route(path: '/{companyId}/members/{userId}', requirements: [
        'companyId' => Requirement::UUID_V1,
        'userId' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
    public function updateMembershipAction(Request $request, string $companyId, string $userId): Response
    {
        $payload = $request->toArray();

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->updateMembership(
                $companyId,
                $userId,
                isset($payload['role']) ? (string)$payload['role'] : null,
                isset($payload['status']) ? (string)$payload['status'] : null,
            ),
            $this->getResource(),
        );
    }

    #[Route(path: '/{companyId}/members/{userId}', requirements: [
        'companyId' => Requirement::UUID_V1,
        'userId' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_DELETE])]
    public function removeMembershipAction(string $companyId, string $userId): Response
    {
        $this->getResource()->removeMembership($companyId, $userId);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
