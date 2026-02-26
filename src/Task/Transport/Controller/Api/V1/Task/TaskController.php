<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\Task;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Task\Application\DTO\Task\TaskCreate;
use App\Task\Application\DTO\Task\TaskPatch;
use App\Task\Application\DTO\Task\TaskUpdate;
use App\Task\Application\Resource\Interfaces\TaskResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/v1/tasks')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Task Management')]
class TaskController extends Controller
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
        Controller::METHOD_CREATE => TaskCreate::class,
        Controller::METHOD_UPDATE => TaskUpdate::class,
        Controller::METHOD_PATCH => TaskPatch::class,
    ];

    public function __construct(TaskResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
