<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\Project;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\Task\Application\Resource\Interfaces\TaskResourceInterface;
use App\Task\Application\Resource\TaskResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for ProjectTasksController endpoints.
 *
 * @method TaskResource getResource()
 * @method ResponseHandler getResponseHandler()
 * @package App\Task\Transport\Controller\Api\V1\Project
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/projects')]
#[Route(path: '/v1/projects')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Project Management')]
class ProjectTasksController extends Controller
{
    public function __construct(TaskResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/tasks', requirements: [
        'id' => Requirement::UUID_V1,
    ], methods: [Request::METHOD_GET])]
    public function tasksAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->find(criteria: [
                'project' => $id,
            ]),
            $this->getResource(),
        );
    }
}
