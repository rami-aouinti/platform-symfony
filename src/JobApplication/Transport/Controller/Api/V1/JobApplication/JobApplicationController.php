<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\Controller\Api\V1\JobApplication;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\JobApplication\Application\DTO\JobApplication\JobApplicationCreate;
use App\JobApplication\Application\DTO\JobApplication\JobApplicationPatch;
use App\JobApplication\Application\DTO\JobApplication\JobApplicationUpdate;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Application\Resource\JobApplicationResource;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * @method JobApplicationResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/job-applications')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Job Application Management')]
class JobApplicationController extends Controller
{
    use CreateMethod;
    use UpdateMethod;
    use PatchMethod;
    use \App\General\Transport\Rest\Traits\Actions\Authenticated\DeleteAction;
    use \App\General\Transport\Rest\Traits\Actions\Authenticated\FindAction;
    use \App\General\Transport\Rest\Traits\Actions\Authenticated\FindOneAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => JobApplicationCreate::class,
        Controller::METHOD_UPDATE => JobApplicationUpdate::class,
        Controller::METHOD_PATCH => JobApplicationPatch::class,
    ];

    public function __construct(JobApplicationResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['jobOffer', 'candidate', 'status'],
        properties: [
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'candidate', type: 'string', format: 'uuid', example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'accepted', 'rejected', 'withdrawn'], example: 'pending'),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 201, description: 'Job application created', content: new JsonContent(
        properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '0195f8d4-5209-77a5-93ae-9f11dfce290f'),
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'candidate', type: 'string', format: 'uuid', example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri')),
            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'accepted', 'rejected', 'withdrawn'], example: 'pending'),
            new OA\Property(property: 'decidedBy', type: 'string', format: 'uuid', nullable: true, example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
            new OA\Property(property: 'decidedAt', type: 'string', format: 'date-time', nullable: true, example: '2026-02-25T12:45:00+00:00'),
        ],
        type: 'object',
    ))]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PUT])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['jobOffer', 'candidate', 'status'],
        properties: [
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'candidate', type: 'string', format: 'uuid', example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'accepted', 'rejected', 'withdrawn'], example: 'pending'),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Job application updated', content: new JsonContent(type: 'object'))]
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->updateMethod($request, $restDto, $id);
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['jobOffer', 'candidate', 'status'],
        properties: [
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'candidate', type: 'string', format: 'uuid', example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'accepted', 'rejected', 'withdrawn'], example: 'pending'),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Job application patched', content: new JsonContent(type: 'object'))]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }

    #[Route(path: '/{id}/accept', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[OA\RequestBody(required: false, content: new JsonContent(properties: [new OA\Property(property: 'note', type: 'string', nullable: true, example: 'Status changed by recruiter workflow.')], type: 'object', example: ['note' => 'Candidate accepted after final interview.']))]
    #[OA\Response(response: 200, description: 'Application accepted', content: new JsonContent(type: 'object'))]
    public function acceptAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, JobApplicationStatus::ACCEPTED),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/reject', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[OA\RequestBody(required: false, content: new JsonContent(properties: [new OA\Property(property: 'note', type: 'string', nullable: true, example: 'Status changed by recruiter workflow.')], type: 'object', example: ['note' => 'Profile does not match required seniority.']))]
    #[OA\Response(response: 200, description: 'Application rejected', content: new JsonContent(type: 'object'))]
    public function rejectAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, JobApplicationStatus::REJECTED),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/withdraw', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[OA\RequestBody(required: false, content: new JsonContent(properties: [new OA\Property(property: 'note', type: 'string', nullable: true, example: 'Status changed by recruiter workflow.')], type: 'object', example: ['note' => 'Candidate accepted another offer.']))]
    #[OA\Response(response: 200, description: 'Application withdrawn', content: new JsonContent(type: 'object'))]
    public function withdrawAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->withdraw($id),
            $this->getResource(),
        );
    }
}
