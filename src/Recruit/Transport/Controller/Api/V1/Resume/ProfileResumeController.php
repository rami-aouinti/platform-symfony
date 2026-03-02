<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Resume;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\General\Transport\Rest\Traits\Methods\DeleteMethod;
use App\General\Transport\Rest\Traits\Methods\FindOneMethod;
use App\General\Transport\Rest\Traits\Methods\PatchMethod;
use App\General\Transport\Rest\Traits\Methods\SchemaMethod;
use App\General\Transport\Rest\Traits\Methods\UpdateMethod;
use App\Recruit\Application\DTO\Resume\ResumeCreate;
use App\Recruit\Application\DTO\Resume\ResumePatch;
use App\Recruit\Application\DTO\Resume\ResumeUpdate;
use App\Recruit\Application\Resource\Interfaces\ResumeResourceInterface;
use App\Recruit\Application\Resource\ResumeResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method ResumeResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/me/profile/resumes')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Me/Profile - Resume')]
class ProfileResumeController extends Controller
{
    use CreateMethod;
    use DeleteMethod;
    use FindOneMethod;
    use PatchMethod;
    use UpdateMethod;
    use SchemaMethod;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ResumeCreate::class,
        Controller::METHOD_UPDATE => ResumeUpdate::class,
        Controller::METHOD_PATCH => ResumePatch::class,
    ];

    public function __construct(ResumeResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function findAction(Request $request): Response
    {
        $data = $this->getResource()->findMyResumes(
            RequestHandler::getOrderBy($request),
            RequestHandler::getLimit($request),
            RequestHandler::getOffset($request),
            RequestHandler::getSearchTerms($request),
            RequestHandler::getTenant($request),
        );

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    #[Route(path: '', methods: [Request::METHOD_POST])]
    public function createAction(Request $request, ResumeCreate $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }

    #[Route(path: '/schema/{method}', methods: [Request::METHOD_GET])]
    public function schemaAction(Request $request, string $method): Response
    {
        return $this->schemaMethod($request, $method);
    }

    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }

    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PUT])]
    public function updateAction(Request $request, ResumeUpdate $restDto, string $id): Response
    {
        return $this->updateMethod($request, $restDto, $id);
    }

    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_PATCH])]
    public function patchAction(Request $request, ResumePatch $restDto, string $id): Response
    {
        return $this->patchMethod($request, $restDto, $id);
    }

    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_DELETE])]
    public function deleteAction(Request $request, string $id): Response
    {
        return $this->deleteMethod($request, $id);
    }
}
