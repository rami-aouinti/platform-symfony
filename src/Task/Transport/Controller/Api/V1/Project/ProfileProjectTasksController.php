<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\Project;

use App\General\Transport\Rest\Controller;
use App\Task\Application\DTO\Task\TaskCreate;
use App\Task\Application\DTO\Task\TaskPatch;
use App\Task\Application\DTO\Task\TaskUpdate;
use App\Task\Application\DTO\TaskRequest\TaskRequestCreate;
use App\Task\Application\DTO\TaskRequest\TaskRequestPatch;
use App\Task\Application\DTO\TaskRequest\TaskRequestUpdate;
use App\Task\Application\Resource\Interfaces\ProjectResourceInterface;
use App\Task\Application\Resource\Interfaces\TaskRequestResourceInterface;
use App\Task\Application\Resource\Interfaces\TaskResourceInterface;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function array_map;

#[AsController]
#[Route(path: '/v1/me/profile/project')]
#[IsGranted('ROLE_LOGGED')]
#[OA\Tag(name: 'Me/Profile - Project Tasks')]
class ProfileProjectTasksController extends Controller
{
    public function __construct(
        private readonly ProjectResourceInterface $projectResource,
        private readonly TaskResourceInterface $taskResource,
        private readonly TaskRequestResourceInterface $taskRequestResource,
    ) {
        parent::__construct($taskResource);
    }

    #[Route(path: '/{projectId}/tasks', requirements: ['projectId' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    public function listProjectTasksAction(Request $request, string $projectId): Response
    {
        $project = $this->resolveProject($projectId);
        $tasks = $this->taskResource->find(criteria: ['project' => $project->getId()]);
        $taskIds = array_map(static fn (Task $task): string => $task->getId(), $tasks);

        $taskRequests = $taskIds === []
            ? []
            : $this->taskRequestResource->find(criteria: ['task' => $taskIds]);

        return $this->getResponseHandler()->createResponse($request, [
            'tasks' => $tasks,
            'taskRequests' => $taskRequests,
        ], $this->taskResource);
    }

    #[Route(path: '/{projectId}/tasks', requirements: ['projectId' => Requirement::UUID_V1], methods: [Request::METHOD_POST])]
    public function createTaskAction(Request $request, TaskCreate $restDto, string $projectId): Response
    {
        $restDto->setProject($this->resolveProject($projectId));
        $task = $this->taskResource->create($restDto, flush: true);

        return $this->getResponseHandler()->createResponse($request, $task, $this->taskResource, Response::HTTP_CREATED);
    }

    #[Route(path: '/{projectId}/tasks/{taskId}', requirements: ['projectId' => Requirement::UUID_V1, 'taskId' => Requirement::UUID_V1], methods: [Request::METHOD_PUT])]
    public function updateTaskAction(Request $request, TaskUpdate $restDto, string $projectId, string $taskId): Response
    {
        $project = $this->resolveProject($projectId);
        $task = $this->resolveProjectTask($taskId, $projectId);
        $restDto->setProject($project);

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->taskResource->update($task->getId(), $restDto, true),
            $this->taskResource,
        );
    }

    #[Route(path: '/{projectId}/tasks/{taskId}', requirements: ['projectId' => Requirement::UUID_V1, 'taskId' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function patchTaskAction(Request $request, TaskPatch $restDto, string $projectId, string $taskId): Response
    {
        $project = $this->resolveProject($projectId);
        $task = $this->resolveProjectTask($taskId, $projectId);
        $restDto->setProject($project);

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->taskResource->patch($task->getId(), $restDto, true),
            $this->taskResource,
        );
    }

    #[Route(path: '/{projectId}/tasks/{taskId}', requirements: ['projectId' => Requirement::UUID_V1, 'taskId' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    public function deleteTaskAction(Request $request, string $projectId, string $taskId): Response
    {
        $task = $this->resolveProjectTask($taskId, $projectId);

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->taskResource->delete($task->getId()),
            $this->taskResource,
        );
    }

    #[Route(path: '/{projectId}/task-requests', requirements: ['projectId' => Requirement::UUID_V1], methods: [Request::METHOD_POST])]
    public function createTaskRequestAction(Request $request, TaskRequestCreate $restDto, string $projectId): Response
    {
        $this->assertTaskBelongsToProject($restDto->getTask(), $projectId);

        $taskRequest = $this->taskRequestResource->create($restDto, flush: true);

        return $this->getResponseHandler()->createResponse($request, $taskRequest, $this->taskRequestResource, Response::HTTP_CREATED);
    }

    #[Route(path: '/{projectId}/task-requests/{taskRequestId}', requirements: ['projectId' => Requirement::UUID_V1, 'taskRequestId' => Requirement::UUID_V1], methods: [Request::METHOD_PUT])]
    public function updateTaskRequestAction(
        Request $request,
        TaskRequestUpdate $restDto,
        string $projectId,
        string $taskRequestId,
    ): Response {
        $taskRequest = $this->resolveProjectTaskRequest($taskRequestId, $projectId);

        if ($restDto->getTask() !== null) {
            $this->assertTaskBelongsToProject($restDto->getTask(), $projectId);
        }

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->taskRequestResource->update($taskRequest->getId(), $restDto, true),
            $this->taskRequestResource,
        );
    }

    #[Route(path: '/{projectId}/task-requests/{taskRequestId}', requirements: ['projectId' => Requirement::UUID_V1, 'taskRequestId' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function patchTaskRequestAction(
        Request $request,
        TaskRequestPatch $restDto,
        string $projectId,
        string $taskRequestId,
    ): Response {
        $taskRequest = $this->resolveProjectTaskRequest($taskRequestId, $projectId);

        if ($restDto->getTask() !== null) {
            $this->assertTaskBelongsToProject($restDto->getTask(), $projectId);
        }

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->taskRequestResource->patch($taskRequest->getId(), $restDto, true),
            $this->taskRequestResource,
        );
    }

    #[Route(path: '/{projectId}/task-requests/{taskRequestId}', requirements: ['projectId' => Requirement::UUID_V1, 'taskRequestId' => Requirement::UUID_V1], methods: [Request::METHOD_DELETE])]
    public function deleteTaskRequestAction(Request $request, string $projectId, string $taskRequestId): Response
    {
        $taskRequest = $this->resolveProjectTaskRequest($taskRequestId, $projectId);

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->taskRequestResource->delete($taskRequest->getId()),
            $this->taskRequestResource,
        );
    }

    private function resolveProject(string $projectId): Project
    {
        $project = $this->projectResource->findOne($projectId);

        if (!$project instanceof Project) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Project not found.');
        }

        return $project;
    }

    private function resolveProjectTask(string $taskId, string $projectId): Task
    {
        $task = $this->taskResource->findOne($taskId);

        if (!$task instanceof Task || $task->getProject()?->getId() !== $projectId) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Task not found for this project.');
        }

        return $task;
    }

    private function resolveProjectTaskRequest(string $taskRequestId, string $projectId): TaskRequest
    {
        $taskRequest = $this->taskRequestResource->findOne($taskRequestId);

        if (!$taskRequest instanceof TaskRequest || $taskRequest->getTask()?->getProject()?->getId() !== $projectId) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Task request not found for this project.');
        }

        return $taskRequest;
    }

    private function assertTaskBelongsToProject(?Task $task, string $projectId): void
    {
        if (!$task instanceof Task || $task->getProject()?->getId() !== $projectId) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Task must belong to the given project.');
        }
    }
}
