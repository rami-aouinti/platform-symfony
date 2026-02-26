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
use App\Task\Domain\Enum\TaskStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use ValueError;

use function implode;
use function strtolower;
use function sprintf;

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


    #[Route(path: '/{id}/requested-status/{status}', methods: [Request::METHOD_PATCH])]
    public function changeRequestedStatusAction(Request $request, string $id, string $status): Response
    {
        $data = $this->getResource()->changeRequestedStatus($id, $this->resolveRequestedStatus($status));

        return $this->getResponseHandler()->createResponse($request, $data, $this->getResource());
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

    private function resolveRequestedStatus(string $status): TaskStatus
    {
        $normalized = strtolower($status);

        if ($normalized === 'pending') {
            return TaskStatus::TODO;
        }

        try {
            return TaskStatus::from($normalized);
        } catch (ValueError) {
            throw new HttpException(
                Response::HTTP_BAD_REQUEST,
                sprintf('Invalid requested status value. Allowed values: %s.', implode(', ', TaskStatus::getValues())),
            );
        }
    }
}

