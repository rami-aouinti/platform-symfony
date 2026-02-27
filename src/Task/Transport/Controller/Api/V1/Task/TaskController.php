<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\Task;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\Task\Application\DTO\Task\TaskCreate;
use App\Task\Application\DTO\Task\TaskPatch;
use App\Task\Application\DTO\Task\TaskUpdate;
use App\Task\Application\Resource\Interfaces\TaskResourceInterface;
use App\Task\Application\Resource\TaskResource;
use App\Task\Domain\Enum\TaskStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for TaskController endpoints.
 *
 * @method TaskResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Task\Transport\Controller\Api\V1\Task
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/tasks')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Task Management')]
class TaskController extends Controller
{
    private const string READ_CACHE_SCOPE = 'task';
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;
    use Actions\Authenticated\SchemaAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => TaskCreate::class,
        Controller::METHOD_UPDATE => TaskUpdate::class,
        Controller::METHOD_PATCH => TaskPatch::class,
    ];

    public function __construct(TaskResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    protected function getReadCacheScope(): ?string
    {
        return self::READ_CACHE_SCOPE;
    }

    #[Route(path: '/{id}/start', methods: [Request::METHOD_PATCH])]
    public function startAction(Request $request, string $id): Response
    {
        return $this->changeStatusAction($request, $id, TaskStatus::IN_PROGRESS);
    }

    #[Route(path: '/{id}/complete', methods: [Request::METHOD_PATCH])]
    public function completeAction(Request $request, string $id): Response
    {
        return $this->changeStatusAction($request, $id, TaskStatus::DONE);
    }

    #[Route(path: '/{id}/archive', methods: [Request::METHOD_PATCH])]
    public function archiveAction(Request $request, string $id): Response
    {
        return $this->changeStatusAction($request, $id, TaskStatus::ARCHIVED);
    }

    #[Route(path: '/{id}/reopen', methods: [Request::METHOD_PATCH])]
    public function reopenAction(Request $request, string $id): Response
    {
        return $this->changeStatusAction($request, $id, TaskStatus::TODO);
    }

    private function changeStatusAction(Request $request, string $id, TaskStatus $status): Response
    {
        $task = $this->getResource()->changeStatus($id, $status);

        $response = $this->getResponseHandler()->createResponse($request, $task, $this->getResource());
        $this->invalidateReadEndpointCache();

        return $response;
    }
}
