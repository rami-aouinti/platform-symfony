<?php

declare(strict_types=1);

namespace App\Calendar\Transport\Controller\Api\V1\Event;

use App\Calendar\Application\DTO\Event\Event;
use App\Calendar\Application\Resource\Interfaces\EventResourceInterface;
use App\General\Transport\Rest\CrudController;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * API controller for EventController endpoints.
 *
 * @package App\Calendar\Transport\Controller\Api\V1\Event
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
#[AsController]
#[Route(path: '/v1/calendar/events')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Calendar Event Management')]
class EventController extends CrudController
{
    protected static string $dtoBaseClass = Event::class;

    public function __construct(EventResourceInterface $resource)
    {
        parent::__construct($resource);
    }
}
