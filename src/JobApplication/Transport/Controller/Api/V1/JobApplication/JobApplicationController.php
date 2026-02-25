<?php

declare(strict_types=1);

namespace App\JobApplication\Transport\Controller\Api\V1\JobApplication;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\General\Transport\Rest\Traits\Actions;
use App\JobApplication\Application\DTO\JobApplication\JobApplicationCreate;
use App\JobApplication\Application\DTO\JobApplication\JobApplicationPatch;
use App\JobApplication\Application\DTO\JobApplication\JobApplicationUpdate;
use App\JobApplication\Application\Resource\Interfaces\JobApplicationResourceInterface;
use App\JobApplication\Application\Resource\JobApplicationResource;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method JobApplicationResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/job-applications')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Job Application Management')]
class JobApplicationController extends Controller
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
        Controller::METHOD_CREATE => JobApplicationCreate::class,
        Controller::METHOD_UPDATE => JobApplicationUpdate::class,
        Controller::METHOD_PATCH => JobApplicationPatch::class,
    ];

    public function __construct(JobApplicationResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    #[Route(path: '/{id}/accept', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function acceptAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, JobApplicationStatus::ACCEPTED),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/reject', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function rejectAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->decide($id, JobApplicationStatus::REJECTED),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/withdraw', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function withdrawAction(Request $request, string $id): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->getResource()->withdraw($id),
            $this->getResource(),
        );
    }
}
