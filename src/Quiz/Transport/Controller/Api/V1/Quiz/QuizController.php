<?php

declare(strict_types=1);

namespace App\Quiz\Transport\Controller\Api\V1\Quiz;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use App\Quiz\Application\DTO\Quiz\QuizCreate;
use App\Quiz\Application\DTO\Quiz\QuizPatch;
use App\Quiz\Application\DTO\Quiz\QuizUpdate;
use App\Quiz\Application\Resource\Interfaces\QuizResourceInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/v1/quizzes')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Quiz Management')]
class QuizController extends Controller
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
        Controller::METHOD_CREATE => QuizCreate::class,
        Controller::METHOD_UPDATE => QuizUpdate::class,
        Controller::METHOD_PATCH => QuizPatch::class,
    ];

    public function __construct(QuizResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
