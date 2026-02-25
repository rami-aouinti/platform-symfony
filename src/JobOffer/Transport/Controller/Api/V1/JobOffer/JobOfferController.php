<?php

declare(strict_types=1);

namespace App\JobOffer\Transport\Controller\Api\V1\JobOffer;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\JobOffer\Application\DTO\JobOffer\JobOfferCreate;
use App\JobOffer\Application\DTO\JobOffer\JobOfferPatch;
use App\JobOffer\Application\DTO\JobOffer\JobOfferUpdate;
use App\JobOffer\Application\Resource\Interfaces\JobOfferResourceInterface;
use App\JobOffer\Application\Resource\JobOfferResource;
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
use Throwable;

/**
 * @method JobOfferResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/job-offers')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Job Offer Management')]
class JobOfferController extends Controller
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
        Controller::METHOD_CREATE => JobOfferCreate::class,
        Controller::METHOD_UPDATE => JobOfferUpdate::class,
        Controller::METHOD_PATCH => JobOfferPatch::class,
    ];

    public function __construct(
        JobOfferResourceInterface $resource,
        private readonly ReadEndpointCache $readEndpointCache,
    ) {
        parent::__construct($resource);
    }

    private const string CACHE_SCOPE = "job_offer";

    /**
     * @throws Throwable
     */
    #[Route(path: '', methods: [Request::METHOD_POST])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['title', 'description', 'location', 'employmentType', 'status', 'company'],
        properties: [
            new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Senior Backend Engineer'),
            new OA\Property(property: 'description', type: 'string', example: 'Design and maintain scalable Symfony services.'),
            new OA\Property(property: 'location', type: 'string', maxLength: 255, example: 'Paris, France'),
            new OA\Property(property: 'employmentType', type: 'string', maxLength: 64, example: 'full-time'),
            new OA\Property(property: 'status', type: 'string', enum: ['draft', 'open', 'closed'], example: 'open'),
            new OA\Property(property: 'company', type: 'string', format: 'uuid', example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 201, description: 'Job offer created', content: new JsonContent(
        properties: [
            new OA\Property(property: 'id', type: 'string', format: 'uuid', example: '0195f7ac-199f-7188-bc2c-fe59f1161b08'),
            new OA\Property(property: 'title', type: 'string', example: 'Senior Backend Engineer'),
            new OA\Property(property: 'description', type: 'string', example: 'Design and maintain scalable Symfony services.'),
            new OA\Property(property: 'location', type: 'string', example: 'Paris, France'),
            new OA\Property(property: 'employmentType', type: 'string', example: 'full-time'),
            new OA\Property(property: 'status', type: 'string', enum: ['draft', 'open', 'closed'], example: 'open'),
            new OA\Property(property: 'company', type: 'string', format: 'uuid', example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
            new OA\Property(property: 'createdBy', type: 'string', format: 'uuid', nullable: true, example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
        ],
        type: 'object',
    ))]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        $response = $this->createMethod($request, $restDto);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PUT])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['title', 'description', 'location', 'employmentType', 'status', 'company'],
        properties: [
            new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Senior Backend Engineer'),
            new OA\Property(property: 'description', type: 'string', example: 'Design and maintain scalable Symfony services.'),
            new OA\Property(property: 'location', type: 'string', maxLength: 255, example: 'Paris, France'),
            new OA\Property(property: 'employmentType', type: 'string', maxLength: 64, example: 'full-time'),
            new OA\Property(property: 'status', type: 'string', enum: ['draft', 'open', 'closed'], example: 'open'),
            new OA\Property(property: 'company', type: 'string', format: 'uuid', example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Job offer updated', content: new JsonContent(type: 'object'))]
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        $response = $this->updateMethod($request, $restDto, $id);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\RequestBody(required: true, content: new JsonContent(
        required: ['title', 'description', 'location', 'employmentType', 'status', 'company'],
        properties: [
            new OA\Property(property: 'title', type: 'string', maxLength: 255, example: 'Senior Backend Engineer'),
            new OA\Property(property: 'description', type: 'string', example: 'Design and maintain scalable Symfony services.'),
            new OA\Property(property: 'location', type: 'string', maxLength: 255, example: 'Paris, France'),
            new OA\Property(property: 'employmentType', type: 'string', maxLength: 64, example: 'full-time'),
            new OA\Property(property: 'status', type: 'string', enum: ['draft', 'open', 'closed'], example: 'open'),
            new OA\Property(property: 'company', type: 'string', format: 'uuid', example: '0195f7a1-8e09-7f40-93f0-c3bcf2b42744'),
        ],
        type: 'object',
    ))]
    #[OA\Response(response: 200, description: 'Job offer patched', content: new JsonContent(type: 'object'))]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
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
        $this->validateRestMethod($request, [Request::METHOD_GET]);

        $orderBy = RequestHandler::getOrderBy($request);
        $limit = RequestHandler::getLimit($request);
        $offset = RequestHandler::getOffset($request);
        $search = RequestHandler::getSearchTerms($request);
        $criteria = RequestHandler::getCriteria($request);
        $entityManagerName = RequestHandler::getTenant($request);

        $this->processCriteria($criteria, $request, __METHOD__);

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
            fn (): array => $this->getResource()->find($criteria, $orderBy, $limit, $offset, $search, $entityManagerName),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function findOneAction(Request $request, string $id): Response
    {
        $entityManagerName = RequestHandler::getTenant($request);

        $data = $this->readEndpointCache->remember(
            self::CACHE_SCOPE,
            $request,
            [
                'criteria' => ['id' => $id],
                'orderBy' => [],
                'limit' => null,
                'offset' => null,
                'search' => [],
                'tenant' => $entityManagerName,
            ],
            fn (): object|null => $this->getResource()->findOne($id, true, $entityManagerName),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    public function deleteAction(Request $request, string $id): Response
    {
        $response = $this->deleteMethod($request, $id);
        $this->readEndpointCache->invalidate(self::CACHE_SCOPE);

        return $response;
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/my', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Job offers created by the current user or manageable with current permissions.',
        content: new JsonContent(type: 'array', items: new OA\Items(type: 'object')),
    )]
    public function findMyAction(Request $request): Response
    {
        return $this->findWithCustomResourceMethod(
            $request,
            fn (
                array $criteria,
                array $orderBy,
                ?int $limit,
                ?int $offset,
                array $search,
                ?string $entityManagerName,
            ): array => $this->getResource()->findMyOffers($criteria, $orderBy, $limit, $offset, $search, $entityManagerName),
        );
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/available', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Open job offers that the current user can apply for.',
        content: new JsonContent(type: 'array', items: new OA\Items(type: 'object')),
    )]
    public function findAvailableAction(Request $request): Response
    {
        return $this->findWithCustomResourceMethod(
            $request,
            fn (
                array $criteria,
                array $orderBy,
                ?int $limit,
                ?int $offset,
                array $search,
                ?string $entityManagerName,
            ): array => $this->getResource()->findAvailableOffers($criteria, $orderBy, $limit, $offset, $search, $entityManagerName),
        );
    }

    /**
     * @param callable(array<string, mixed>, array<string, string>, ?int, ?int, array<int, string>, ?string): array<int, object> $resolver
     *
     * @throws Throwable
     */
    private function findWithCustomResourceMethod(Request $request, callable $resolver): Response
    {
        $this->validateRestMethod($request, [Request::METHOD_GET]);

        $orderBy = RequestHandler::getOrderBy($request);
        $limit = RequestHandler::getLimit($request);
        $offset = RequestHandler::getOffset($request);
        $search = RequestHandler::getSearchTerms($request);

        try {
            $criteria = RequestHandler::getCriteria($request);
            $entityManagerName = RequestHandler::getTenant($request);
            $this->processCriteria($criteria, $request, __METHOD__);

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
                fn (): array => $resolver($criteria, $orderBy, $limit, $offset, $search, $entityManagerName),
            );

            return $this->getResponseHandler()->createResponse(
                $request,
                $data,
                $this->getResource(),
            );
        } catch (Throwable $exception) {
            throw $this->handleRestMethodException(
                exception: $exception,
                entityManagerName: $entityManagerName ?? null,
            );
        }
    }
}
