<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\TaskRequest;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Task\Application\DTO\TaskRequest\TaskRequestCreate;
use App\Task\Application\DTO\TaskRequest\TaskRequestPatch;
use App\Task\Application\DTO\TaskRequest\TaskRequestUpdate;
use App\Task\Application\Resource\Interfaces\TaskRequestResourceInterface;
use App\Task\Application\Resource\TaskRequestResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method TaskRequestResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/task-requests')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Task Request Management')]
class TaskRequestController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => TaskRequestCreate::class,
        Controller::METHOD_UPDATE => TaskRequestUpdate::class,
        Controller::METHOD_PATCH => TaskRequestPatch::class,
    ];

    public function __construct(TaskRequestResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/approve', methods: [Request::METHOD_PATCH])]
    public function approveAction(Request $request, string $id): Response
    {
        $data = $this->getResource()->approve($id);

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    #[Route(path: '/{id}/reject', methods: [Request::METHOD_PATCH])]
    public function rejectAction(Request $request, string $id): Response
    {
        $data = $this->getResource()->reject($id);

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }

    #[Route(path: '/{id}/cancel', methods: [Request::METHOD_PATCH])]
    public function cancelAction(Request $request, string $id): Response
    {
        $data = $this->getResource()->cancel($id);

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
    }
}
