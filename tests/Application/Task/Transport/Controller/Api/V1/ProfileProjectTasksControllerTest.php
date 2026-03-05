<?php

declare(strict_types=1);

namespace App\Tests\Application\Task\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileProjectTasksControllerTest extends WebTestCase
{
    private const string PROJECTS_URL = self::API_URL_PREFIX . '/v1/me/profile/projects';
    private const string PROJECT_SCOPE_URL = self::API_URL_PREFIX . '/v1/me/profile/project';

    /**
     * @throws Throwable
     */
    public function testLoggedUserCanManageTasksAndTaskRequestsWithinOwnProject(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('POST', self::PROJECTS_URL, content: JSON::encode([
            'name' => 'Scoped Tasks Project',
            'status' => 'active',
            'description' => 'Project for scoped task endpoints.',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $project = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($project);

        $projectId = $project['id'];
        self::assertIsString($projectId);

        $client->request('POST', self::PROJECT_SCOPE_URL . '/' . $projectId . '/tasks', content: JSON::encode([
            'title' => 'Scoped Task',
            'description' => 'Created within project scope.',
            'priority' => 'medium',
            'status' => 'todo',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $task = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($task);

        $taskId = $task['id'];
        self::assertIsString($taskId);

        $client->request('PATCH', self::PROJECT_SCOPE_URL . '/' . $projectId . '/tasks/' . $taskId, content: JSON::encode([
            'title' => 'Scoped Task Updated',
        ]));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $updatedTask = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame('Scoped Task Updated', $updatedTask['title'] ?? null);

        $client->request('POST', self::PROJECT_SCOPE_URL . '/' . $projectId . '/task-requests', content: JSON::encode([
            'task' => $taskId,
            'type' => 'status_change',
            'requestedStatus' => 'in_progress',
            'note' => 'Please start this task.',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $taskRequest = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($taskRequest);

        $taskRequestId = $taskRequest['id'];
        self::assertIsString($taskRequestId);

        $client->request('PATCH', self::PROJECT_SCOPE_URL . '/' . $projectId . '/task-requests/' . $taskRequestId, content: JSON::encode([
            'note' => 'Updated request note.',
        ]));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $updatedTaskRequest = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame('Updated request note.', $updatedTaskRequest['note'] ?? null);

        $client->request('GET', self::PROJECT_SCOPE_URL . '/' . $projectId . '/tasks');
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $listPayload = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($listPayload);
        self::assertArrayHasKey('tasks', $listPayload);
        self::assertArrayHasKey('taskRequests', $listPayload);

        $taskIds = array_column($listPayload['tasks'], 'id');
        self::assertContains($taskId, $taskIds);

        $taskRequestIds = array_column($listPayload['taskRequests'], 'id');
        self::assertContains($taskRequestId, $taskRequestIds);

        $client->request('DELETE', self::PROJECT_SCOPE_URL . '/' . $projectId . '/task-requests/' . $taskRequestId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('DELETE', self::PROJECT_SCOPE_URL . '/' . $projectId . '/tasks/' . $taskId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    public function testTaskRequestCreationFailsWhenTaskDoesNotBelongToProject(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('POST', self::PROJECTS_URL, content: JSON::encode([
            'name' => 'First Project',
            'status' => 'active',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $firstProject = JSON::decode((string)$client->getResponse()->getContent(), true);

        $client->request('POST', self::PROJECTS_URL, content: JSON::encode([
            'name' => 'Second Project',
            'status' => 'active',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $secondProject = JSON::decode((string)$client->getResponse()->getContent(), true);

        $client->request('POST', self::PROJECT_SCOPE_URL . '/' . $firstProject['id'] . '/tasks', content: JSON::encode([
            'title' => 'Task in first project',
            'priority' => 'medium',
            'status' => 'todo',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $task = JSON::decode((string)$client->getResponse()->getContent(), true);

        $client->request('POST', self::PROJECT_SCOPE_URL . '/' . $secondProject['id'] . '/task-requests', content: JSON::encode([
            'task' => $task['id'],
            'type' => 'status_change',
            'requestedStatus' => 'done',
        ]));

        self::assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }
}
