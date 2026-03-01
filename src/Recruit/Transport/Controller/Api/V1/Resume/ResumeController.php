<?php

declare(strict_types=1);

namespace App\Recruit\Transport\Controller\Api\V1\Resume;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\RequestHandler;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Recruit\Application\DTO\Resume\ResumeCreate;
use App\Recruit\Application\DTO\Resume\ResumePatch;
use App\Recruit\Application\DTO\Resume\ResumeUpdate;
use App\Recruit\Application\Resource\Interfaces\ResumeResourceInterface;
use App\Recruit\Application\Resource\ResumeResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for ResumeController endpoints.
 *
 * @method ResumeResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Recruit\Transport\Controller\Api\V1\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/resumes')]
#[Route(path: '/v1/resumes')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Resume Management')]
class ResumeController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;
    use Actions\Authenticated\SchemaAction;

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

    #[Route(path: '/my', methods: [Request::METHOD_GET])]
    public function findMyAction(Request $request): Response
    {
        $this->validateRestMethod($request, [Request::METHOD_GET]);

        $orderBy = RequestHandler::getOrderBy($request);
        $limit = RequestHandler::getLimit($request);
        $offset = RequestHandler::getOffset($request);
        $search = RequestHandler::getSearchTerms($request);
        $entityManagerName = RequestHandler::getTenant($request);

        $data = $this->getResource()->findMyResumes($orderBy, $limit, $offset, $search, $entityManagerName);

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    #[Route(path: '/{id}', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function findOneAction(Request $request, string $id): Response
    {
        return $this->findOneMethod($request, $id);
    }
}
