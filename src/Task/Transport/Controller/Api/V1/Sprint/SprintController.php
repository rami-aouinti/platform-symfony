<?php

declare(strict_types=1);

namespace App\Task\Transport\Controller\Api\V1\Sprint;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Task\Application\DTO\Sprint\SprintCreate;
use App\Task\Application\DTO\Sprint\SprintPatch;
use App\Task\Application\DTO\Sprint\SprintUpdate;
use App\Task\Application\Resource\Interfaces\SprintResourceInterface;
use App\Task\Application\Resource\SprintResource;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for SprintController endpoints.
 *
 * @method SprintResource getResource()
 * @package App\Task\Transport\Controller\Api\V1\Sprint
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/api/v1/admin/sprints')]
#[Route(path: '/v1/sprints')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Sprint Management')]
class SprintController extends Controller
{
    use Actions\Authenticated\CreateAction;
    use Actions\Authenticated\DeleteAction;
    use Actions\Authenticated\FindAction;
    use Actions\Authenticated\FindOneAction;
    use Actions\Authenticated\PatchAction;
    use Actions\Authenticated\UpdateAction;
    use Actions\Authenticated\SchemaAction;

    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => SprintCreate::class,
        Controller::METHOD_UPDATE => SprintUpdate::class,
        Controller::METHOD_PATCH => SprintPatch::class,
    ];

    public function __construct(SprintResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
