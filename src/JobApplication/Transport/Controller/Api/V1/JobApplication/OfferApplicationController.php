<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\Controller\Api\V1\JobApplication;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\JobApplication\Application\DTO\JobApplication\OfferApplicationPayload;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Application\Resource\JobApplicationResource;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
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

    #[Route(path: '/{id}/apply', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_POST])]
    #[OA\RequestBody(
        required: false,
        content: new JsonContent(
            properties: [
                new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
                new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf', description: 'Legacy external CV URL. Used only when resumeId is absent.'),
                new OA\Property(property: 'resumeId', type: 'string', format: 'uuid', nullable: true, example: '0195f798-7a12-7303-8db6-ece0cabf335d', description: 'Internal resume identifier. Has priority over cvUrl when both are provided.'),
                new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
            ],
            type: 'object',
            example: [
                'coverLetter' => 'I built high-scale Symfony APIs for 5 years.',
                'cvUrl' => 'https://cdn.example.com/cv/jane-doe.pdf',
                'resumeId' => '0195f798-7a12-7303-8db6-ece0cabf335d',
                'attachments' => ['https://cdn.example.com/portfolio.pdf'],
            ],
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'Application submitted for this offer',
        content: new JsonContent(
            properties: [
                new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '0195f8d4-5209-77a5-93ae-9f11dfce290f'),
                new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
                new OA\Property(property: 'candidate', type: 'string', format: 'uuid', example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
                new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
                new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
                new OA\Property(property: 'resume', type: 'string', format: 'uuid', nullable: true, example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
                new OA\Property(property: 'resumeId', type: 'string', format: 'uuid', nullable: true, example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
                new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri')),
                new OA\Property(property: 'status', type: 'string', enum: ['pending', 'accepted', 'rejected', 'withdrawn'], example: 'pending'),
                new OA\Property(property: 'decidedBy', type: 'string', format: 'uuid', nullable: true, example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
                new OA\Property(property: 'decidedAt', type: 'string', format: 'date-time', nullable: true, example: '2026-02-25T12:45:00+00:00'),
            ],
            type: 'object',
        ),
    )]
    public function createForOfferAction(Request $request, string $id): Response
    {
        $payload = OfferApplicationPayload::fromArray($request->getContent() === '' ? [] : $request->toArray());

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->apply($id, $payload),
            $this->getResource(),
            Response::HTTP_CREATED,
        );
    }
}
