<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\JobApplication;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\Recruit\Application\DTO\JobApplication\JobApplicationApply;
use App\Recruit\Application\DTO\JobApplication\JobApplicationUpdate;
use App\Recruit\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\Recruit\Application\Resource\JobApplicationResource;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\Tool\Application\Service\Rest\ReadEndpointCache;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

use function array_intersect;
use function array_keys;
use function implode;

/**
 * @method JobApplicationResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
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

    private const string CACHE_SCOPE = 'job_application';

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => JobApplicationApply::class,
        Controller::METHOD_UPDATE => JobApplicationUpdate::class,
        Controller::METHOD_PATCH => JobApplicationApply::class,
    ];

    public function __construct(
        JobApplicationResourceInterface $resource,
        ReadEndpointCache $readEndpointCache,
    ) {
        parent::__construct($resource);
        $this->readEndpointCache = $readEndpointCache;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['jobOffer'],
        properties: [
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 201, description: 'Job application created', content: new JsonContent(
        properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '0195f8d4-5209-77a5-93ae-9f11dfce290f'),
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'candidate', type: 'string', format: 'uuid', example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'accepted', 'rejected', 'withdrawn'], example: 'pending'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri')),
            new OA\Property(property: 'decidedBy', type: 'string', format: 'uuid', nullable: true, example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
            new OA\Property(property: 'decidedAt', type: 'string', format: 'date-time', nullable: true, example: '2026-02-25T12:45:00+00:00'),
        ],
        type: 'object',
    ))]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        $this->assertNoCandidateManagedFields($request);

        $response = $this->createMethod($request, $restDto);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PUT])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['jobOffer'],
        properties: [
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Job application updated', content: new JsonContent(type: 'object'))]
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        $response = $this->updateMethod($request, $restDto, $id);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['jobOffer'],
        properties: [
            new OA\Property(property: 'jobOffer', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'coverLetter', type: 'string', nullable: true, example: 'I built high-scale Symfony APIs for 5 years.'),
            new OA\Property(property: 'cvUrl', type: 'string', format: 'uri', nullable: true, example: 'https://cdn.example.com/cv/jane-doe.pdf'),
            new OA\Property(property: 'attachments', type: 'array', nullable: true, items: new OA\Items(type: 'string', format: 'uri'), example: ['https://cdn.example.com/portfolio.pdf']),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Job application patched', content: new JsonContent(type: 'object'))]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        $this->assertNoCandidateManagedFields($request);

        $response = $this->patchMethod($request, $restDto, $id);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function findAction(Request $request): Response
    {
        $orderBy = RequestHandler::getOrderBy($request);
        $limit = RequestHandler::getLimit($request);
        $offset = RequestHandler::getOffset($request);
        $search = RequestHandler::getSearchTerms($request);
        $criteria = RequestHandler::getCriteria($request);
        $entityManagerName = RequestHandler::getTenant($request);

        $data = $this->readEndpointCache->remember(
            self::CACHE_SCOPE,
            $request,
            [
                'criteria' => $criteria,
                'orderBy' => $orderBy,
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search,
                'tenant' => $entityManagerName,
            ],
            fn (): array => $this->getResource()->findAllowedForCurrentUser(
                $criteria,
                $orderBy,
                $limit,
                $offset,
                $search,
                $entityManagerName,
            ),
        );

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->getResource(),
        );
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/my-offers', methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Response(
        response: 200,
        description: 'Job applications linked to offers managed by the current user, so they can accept or reject them.',
        content: new JsonContent(type: 'array', items: new OA\Items(type: 'object')),
    )]
    public function findForMyOffersAction(Request $request): Response
    {
        $entityManagerName = RequestHandler::getTenant($request);
        $data = $this->readEndpointCache->remember(
            self::CACHE_SCOPE,
            $request,
            [
                'criteria' => [
                    'scope' => 'my-offers',
                ],
                'orderBy' => [],
                'limit' => null,
                'offset' => null,
                'search' => [],
                'tenant' => $entityManagerName,
            ],
            fn (): array => $this->getResource()->findForMyOffers(),
        );

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->getResource(),
        );
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function findOneAction(Request $request, string $id): Response
    {
        $entityManagerName = RequestHandler::getTenant($request);
        $data = $this->readEndpointCache->remember(
            self::CACHE_SCOPE,
            $request,
            [
                'criteria' => [
                    'id' => $id,
                ],
                'orderBy' => [],
                'limit' => null,
                'offset' => null,
                'search' => [],
                'tenant' => $entityManagerName,
            ],
            fn (): object => $this->getResource()->getAllowedForCurrentUser($id),
        );

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->getResource(),
        );
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_DELETE])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function deleteAction(Request $request, string $id): Response
    {
        $response = $this->deleteMethod($request, $id);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws BadRequestHttpException
     */
    private function assertNoCandidateManagedFields(Request $request): void
    {
        $forbiddenFields = ['candidate', 'status', 'decidedBy', 'decidedAt'];
        $submittedFields = array_keys($request->request->all());
        $invalidFields = array_intersect($forbiddenFields, $submittedFields);

        if ($invalidFields !== []) {
            throw new BadRequestHttpException('Fields not allowed in candidate flow: ' . implode(', ', $invalidFields) . '.');
        }
    }

    #[Route(path: '/{id}/accept', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
    #[OA\RequestBody(required: false, content: new JsonContent(properties: [new OA\Property(property: 'note', type: 'string', nullable: true, example: 'Status changed by recruiter workflow.')], type: 'object', example: [
        'note' => 'Candidate accepted after final interview.',
    ]))]
    #[OA\Response(response: 200, description: 'Application accepted', content: new JsonContent(type: 'object'))]
    public function acceptAction(Request $request, string $id): Response
    {
        $data = $this->getResource()->decide($id, JobApplicationStatus::ACCEPTED);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/reject', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
    #[OA\RequestBody(required: false, content: new JsonContent(properties: [new OA\Property(property: 'note', type: 'string', nullable: true, example: 'Status changed by recruiter workflow.')], type: 'object', example: [
        'note' => 'Profile does not match required seniority.',
    ]))]
    #[OA\Response(response: 200, description: 'Application rejected', content: new JsonContent(type: 'object'))]
    public function rejectAction(Request $request, string $id): Response
    {
        $data = $this->getResource()->decide($id, JobApplicationStatus::REJECTED);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/withdraw', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
    #[OA\RequestBody(required: false, content: new JsonContent(properties: [new OA\Property(property: 'note', type: 'string', nullable: true, example: 'Status changed by recruiter workflow.')], type: 'object', example: [
        'note' => 'Candidate accepted another offer.',
    ]))]
    #[OA\Response(response: 200, description: 'Application withdrawn', content: new JsonContent(type: 'object'))]
    public function withdrawAction(Request $request, string $id): Response
    {
        $data = $this->getResource()->withdraw($id);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->getResource(),
        );
    }
}
