<?php

declare(strict_types=1);

namespace App\Calendar\Transport\Controller\Api\V1\Event;

use App\Calendar\Application\DTO\Event\EventCreate;
use App\Calendar\Application\DTO\Event\EventPatch;
use App\Calendar\Application\DTO\Event\EventUpdate;
use App\Calendar\Application\Resource\Interfaces\EventResourceInterface;
use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\Traits\Actions;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/v1/calendar/events')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Calendar Event Management')]
class EventController extends Controller
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
        Controller::METHOD_CREATE => EventCreate::class,
        Controller::METHOD_UPDATE => EventUpdate::class,
        Controller::METHOD_PATCH => EventPatch::class,
    ];

    public function __construct(EventResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
