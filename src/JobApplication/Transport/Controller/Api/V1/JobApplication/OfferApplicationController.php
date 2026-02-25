<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\Controller\Api\V1\JobApplication;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Application\Resource\JobApplicationResource;
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
#[Route(path: '/v1/job-offers')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Job Application Management')]
class OfferApplicationController extends Controller
{
    public function __construct(JobApplicationResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{jobOfferId}/applications', requirements: ['jobOfferId' => Requirement::UUID_V1], methods: [Request::METHOD_POST])]
    public function createForOfferAction(Request $request, string $jobOfferId): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->apply($jobOfferId),
            $this->getResource(),
            Response::HTTP_CREATED,
        );
    }
}
