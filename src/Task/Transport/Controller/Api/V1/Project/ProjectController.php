<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\Project;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Task\Application\DTO\Project\ProjectCreate;
use App\Task\Application\DTO\Project\ProjectPatch;
use App\Task\Application\DTO\Project\ProjectUpdate;
use App\Task\Application\Resource\Interfaces\ProjectResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for ProjectController endpoints.
 *
 * @package App\Task\Transport\Controller\Api\V1\Project
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/projects')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Project Management')]
class ProjectController extends Controller
{
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
        Controller::METHOD_CREATE => ProjectCreate::class,
        Controller::METHOD_UPDATE => ProjectUpdate::class,
        Controller::METHOD_PATCH => ProjectPatch::class,
    ];

    public function __construct(ProjectResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
