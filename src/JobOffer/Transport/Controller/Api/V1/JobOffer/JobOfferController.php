<?php

declare(strict_types=1);

namespace App\JobOffer\Transport\Controller\Api\V1\JobOffer;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\JobOffer\Application\DTO\JobOffer\JobOfferCreate;
use App\JobOffer\Application\DTO\JobOffer\JobOfferPatch;
use App\JobOffer\Application\DTO\JobOffer\JobOfferUpdate;
use App\JobOffer\Application\Resource\Interfaces\JobOfferResourceInterface;
use App\JobOffer\Application\Resource\JobOfferResource;
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

    public function __construct(JobOfferResourceInterface $resource)
    {
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
            new OA\Property(property: 'createdBy', type: 'string', format: 'uuid', nullable: true, example: '0195f798-7a12-7303-8db6-ece0cabf335d'),
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
        return $this->updateMethod($request, $restDto, $id);
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
        return $this->patchMethod($request, $restDto, $id);
    }
}
