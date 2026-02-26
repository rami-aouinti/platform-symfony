<?php

declare(strict_types=1);

namespace App\JobOffer\Transport\Controller\Api\V1\JobOffer;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions\Authenticated\DeleteAction;
use App\General\Transport\Rest\Traits\Actions\Authenticated\FindAction;
use App\General\Transport\Rest\Traits\Actions\Authenticated\FindOneAction;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobOffer\Application\DTO\JobOffer\JobOfferCreate;
use App\JobOffer\Application\DTO\JobOffer\JobOfferPatch;
use App\JobOffer\Application\DTO\JobOffer\JobOfferUpdate;
use App\JobOffer\Application\Resource\Interfaces\JobOfferResourceInterface;
use App\JobOffer\Application\Resource\JobOfferResource;
use App\Tool\Application\Service\Rest\ReadEndpointCache;
use DateInterval;
use DateTimeImmutable;
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

use function array_filter;
use function array_map;
use function array_values;
use function explode;
use function in_array;
use function is_array;
use function is_numeric;
use function preg_match;
use function sprintf;
use function trim;

/**
 * @method JobOfferResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\JobOffer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
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
    use DeleteAction;
    use FindAction;
    use FindOneAction;

    private const string CACHE_SCOPE = 'job_offer';
    private const int DEFAULT_LIMIT = 20;
    private const int DEFAULT_OFFSET = 0;

    /**
     * @var array<string, string>
     */
    private const array DEFAULT_ORDER_BY = [
        'publishedAt' => 'DESC',
    ];
    private const string DEFAULT_CANDIDATE_STATUS = 'open';

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
        private readonly JobApplicationResourceInterface $jobApplicationResource,
    ) {
        parent::__construct($resource);
    }

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
            new OA\Property(
                property: 'createdBy',
                type: 'string',
                format: 'uuid',
                example: '0195f798-7a12-7303-8db6-ece0cabf335d',
                nullable: true
            ),
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
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PUT])]
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
    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
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
    #[OA\Parameter(name: 'salaryMin', description: 'Minimum expected salary. Mapped to internal criteria: entity.salaryMax >= salaryMin.', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'salaryMax', description: 'Maximum expected salary. Mapped to internal criteria: entity.salaryMin <= salaryMax.', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'remoteMode', description: 'Business filter mapped to internal criteria key remoteMode (supports alias remotePolicy).', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'employmentType', description: 'Business filter mapped to internal criteria key employmentType.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'workTime', description: 'Business filter mapped to internal criteria key workTime.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'publishedWithinDays', description: 'Keep only offers published within the given number of days. Mapped to entity.publishedAt >= now - N days.', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 0))]
    #[OA\Parameter(name: 'skills', description: 'Business filter applied as post-filter on offer skill ids.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'language', description: 'Business alias of languages[], applied as post-filter on offer language ids.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'languages', description: 'Business filter applied as post-filter on offer language ids.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'city', description: 'Business filter mapped to internal criteria key city.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'region', description: 'Business filter mapped to internal criteria key region.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'jobCategory', description: 'Business filter mapped to internal criteria key jobCategory.', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'status', description: 'Offer status. Default is open for candidate journey.', in: 'query', schema: new OA\Schema(type: 'string', enum: ['draft', 'open', 'closed'], default: 'open'))]
    #[OA\Parameter(name: 'order[publishedAt]', description: 'Sorting. Default is publishedAt DESC.', in: 'query', schema: new OA\Schema(type: 'string', enum: ['ASC', 'DESC'], default: 'DESC'))]
    #[OA\Parameter(name: 'limit', description: 'Pagination size. Default: 20.', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1, default: self::DEFAULT_LIMIT))]
    #[OA\Parameter(name: 'offset', description: 'Pagination offset. Default: 0.', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 0, default: self::DEFAULT_OFFSET))]
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

        [$criteria, $postFilters] = $this->normalizeBusinessQueryParams($request, $criteria);
        $this->applyCandidateSearchDefaults($criteria, $orderBy, $limit, $offset);

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
            fn (): array => $this->applyPostFilters(
                $this->getResource()->find($criteria, $orderBy, $limit, $offset, $search, $entityManagerName),
                $postFilters,
            ),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    /**
     * @throws Throwable
     */
    #[Route(path: '/facets', methods: [Request::METHOD_GET])]
    #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
    #[OA\Parameter(name: 'salaryMin', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'salaryMax', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'remoteMode', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'employmentType', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'workTime', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'publishedWithinDays', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'skills', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'languages', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'city', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'region', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    #[OA\Parameter(name: 'jobCategory', in: 'query', schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'string', format: 'uuid')), style: 'form', explode: true)]
    public function facetsAction(Request $request): Response
    {
        $this->validateRestMethod($request, [Request::METHOD_GET]);

        $criteria = RequestHandler::getCriteria($request);
        $search = RequestHandler::getSearchTerms($request);
        $entityManagerName = RequestHandler::getTenant($request);

        $this->processCriteria($criteria, $request, __METHOD__);
        [$criteria, $postFilters] = $this->normalizeBusinessQueryParams($request, $criteria);

        $orderBy = [];
        $limit = null;
        $offset = null;
        $this->applyCandidateSearchDefaults($criteria, $orderBy, $limit, $offset);

        $data = $this->readEndpointCache->remember(
            self::CACHE_SCOPE,
            $request,
            [
                'criteria' => $criteria,
                'search' => $search,
                'postFilters' => $postFilters,
                'tenant' => $entityManagerName,
            ],
            fn (): array => $this->getResource()->computeFacets(
                $criteria,
                $search,
                $postFilters,
                $entityManagerName,
            ),
        );

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            null,
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
            fn (): object|null => $this->getResource()->findOne($id, true, $entityManagerName),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
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
     * @throws Throwable
     */
    #[Route(path: '/my', methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Job applications linked to offers managed by the current user, so they can accept or reject them.',
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
     * @throws Throwable
     */
    private function findMyApplicationsAction(Request $request): Response
    {
        $entityManagerName = RequestHandler::getTenant($request);

        $data = $this->readEndpointCache->remember(
            self::CACHE_SCOPE,
            $request,
            [
                'criteria' => [
                    'scope' => 'my-offers-applications',
                ],
                'orderBy' => [
                    'createdAt' => 'DESC',
                ],
                'limit' => null,
                'offset' => null,
                'search' => [],
                'tenant' => $entityManagerName,
            ],
            fn (): array => $this->jobApplicationResource->findForMyOffers(),
        );

        return $this->getResponseHandler()->createResponse(
            $request,
            $data,
            $this->jobApplicationResource,
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
            [$criteria, $postFilters] = $this->normalizeBusinessQueryParams($request, $criteria);

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
                fn (): array => $this->applyPostFilters(
                    $resolver($criteria, $orderBy, $limit, $offset, $search, $entityManagerName),
                    $postFilters,
                ),
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

    /**
     * @param array<int|string, mixed> $criteria
     *
     * @return array{0: array<int|string, mixed>, 1: array{skills: array<int, string>, languages: array<int, string>}}
     */
    private function normalizeBusinessQueryParams(Request $request, array $criteria): array
    {
        $languageFilters = $this->normalizeUuidValues([
            ...$this->getQueryList($request, 'language'),
            ...$this->getQueryList($request, 'languages'),
        ]);

        $postFilters = [
            'skills' => $this->normalizeUuidValues($this->getQueryList($request, 'skills')),
            'languages' => $languageFilters,
        ];

        $mappedInFilters = [
            'remoteMode' => 'remoteMode',
            'remotePolicy' => 'remoteMode',
            'employmentType' => 'employmentType',
            'workTime' => 'workTime',
            'city' => 'city',
            'region' => 'region',
            'jobCategory' => 'jobCategory',
        ];

        foreach ($mappedInFilters as $queryKey => $criteriaField) {
            $values = $this->getQueryList($request, $queryKey);

            if (in_array($queryKey, ['skills', 'languages', 'city', 'region', 'jobCategory'], true)) {
                $values = $this->normalizeUuidValues($values);
            }

            if ($values === []) {
                continue;
            }

            $criteria[$criteriaField] = $values;
        }

        $salaryMin = $request->query->get('salaryMin');

        if ($salaryMin !== null && is_numeric((string)$salaryMin)) {
            $criteria[] = ['entity.salaryMax', 'gte', (int)$salaryMin];
        }

        $salaryMax = $request->query->get('salaryMax');

        if ($salaryMax !== null && is_numeric((string)$salaryMax)) {
            $criteria[] = ['entity.salaryMin', 'lte', (int)$salaryMax];
        }

        $publishedWithinDays = $request->query->get('publishedWithinDays');

        if ($publishedWithinDays !== null && is_numeric((string)$publishedWithinDays) && (int)$publishedWithinDays >= 0) {
            $criteria[] = [
                'entity.publishedAt',
                'gte',
                (new DateTimeImmutable())->sub(new DateInterval(sprintf('P%dD', (int)$publishedWithinDays))),
            ];
        }

        return [$criteria, $postFilters];
    }

    private function applyCandidateSearchDefaults(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset): void
    {
        if (!isset($criteria['status']) || $criteria['status'] === null || $criteria['status'] === '') {
            $criteria['status'] = self::DEFAULT_CANDIDATE_STATUS;
        }

        if ($orderBy === []) {
            $orderBy = self::DEFAULT_ORDER_BY;
        }

        $limit ??= self::DEFAULT_LIMIT;
        $offset ??= self::DEFAULT_OFFSET;
    }

    /**
     * @param array<int, object> $offers
     * @param array{skills: array<int, string>, languages: array<int, string>} $postFilters
     *
     * @return array<int, object>
     */
    private function applyPostFilters(array $offers, array $postFilters): array
    {
        return array_values(array_filter($offers, function (object $offer) use ($postFilters): bool {
            if ($postFilters['skills'] !== []) {
                $skillIds = array_map(static fn ($skill): string => $skill->getId(), $offer->getSkills()->toArray());

                if (array_values(array_intersect($postFilters['skills'], $skillIds)) === []) {
                    return false;
                }
            }

            if ($postFilters['languages'] !== []) {
                $languageIds = array_map(static fn ($language): string => $language->getId(), $offer->getLanguages()->toArray());

                if (array_values(array_intersect($postFilters['languages'], $languageIds)) === []) {
                    return false;
                }
            }

            return true;
        }));
    }

    /**
     * @param array<int, string> $values
     *
     * @return array<int, string>
     */
    private function normalizeUuidValues(array $values): array
    {
        return array_values(array_filter($values, static fn (string $value): bool => preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/', $value) === 1));
    }

    /**
     * @return array<int, string>
     */
    private function getQueryList(Request $request, string $name): array
    {
        $rawValue = $request->query->all()[$name] ?? $request->query->get($name);

        if ($rawValue === null || $rawValue === '') {
            return [];
        }

        if (is_array($rawValue)) {
            return array_values(array_filter(array_map(static fn (mixed $value): string => trim((string)$value), $rawValue)));
        }

        return array_values(array_filter(array_map(static fn (string $value): string => trim($value), explode(',', (string)$rawValue))));
    }
}
