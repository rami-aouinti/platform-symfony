<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\Controller\Api\V1\JobApplication;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Application\Resource\JobApplicationResource;
use App\JobApplication\Domain\Enum\ApplicationStatus;
use App\JobApplication\Domain\Exception\JobApplicationException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method JobApplicationResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/applications')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Job Application Management')]
class JobApplicationController extends Controller
{
    public function __construct(JobApplicationResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function listAction(Request $request): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->findAllowedForCurrentUser(),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    public function getAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->getAllowedForCurrentUser($id),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/withdraw', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function withdrawAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->withdraw($id),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/decision', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function decisionAction(Request $request, string $id): Response
    {
        $payload = (array) json_decode($request->getContent(), true);
        $status = ApplicationStatus::tryFrom((string) ($payload['status'] ?? ''));

        if (!$status instanceof ApplicationStatus) {
            throw new JobApplicationException('Field "status" is required and must be a valid status.', Response::HTTP_BAD_REQUEST);
        }

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, $status),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/accept', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function acceptAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, ApplicationStatus::ACCEPTED),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/reject', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function rejectAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, ApplicationStatus::REJECTED),
            $this->getResource(),
        );
    }
}
