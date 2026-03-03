<?php

declare(strict_types=1);

namespace App\Calendar\Transport\Controller\Api\V1\Event;

use App\Calendar\Application\DTO\Event\Event;
use App\Calendar\Application\Resource\Interfaces\EventResourceInterface;
use App\Calendar\Domain\Entity\Event as EventEntity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\CrudController;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

#[AsController]
#[Route(path: '/v1/me/calendar/events')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Me - Calendar Event Management')]
class MeEventController extends CrudController
{
    protected static string $dtoBaseClass = Event::class;

    private ?string $currentUserId = null;

    public function __construct(EventResourceInterface $resource)
    {
        parent::__construct($resource);
    }

    /** @throws Throwable */
    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function findAction(Request $request, User $loggedInUser): Response
    {
        $this->currentUserId = $loggedInUser->getId();

        return $this->findMethod($request);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_GET])]
    public function findOneAction(Request $request, string $id, User $loggedInUser): Response
    {
        $this->assertOwnedEvent($id, $loggedInUser);

        return $this->findOneMethod($request, $id);
    }

    /** @throws Throwable */
    #[Route(path: '', methods: [Request::METHOD_POST])]
    public function createAction(Request $request, RestDtoInterface $restDto, User $loggedInUser): Response
    {
        if ($restDto instanceof Event) {
            $restDto->setUser($loggedInUser);
        }

        return $this->createMethod($request, $restDto);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_PUT])]
    public function updateAction(Request $request, string $id, RestDtoInterface $restDto, User $loggedInUser): Response
    {
        $this->assertOwnedEvent($id, $loggedInUser);

        if ($restDto instanceof Event) {
            $restDto->setUser($loggedInUser);
        }

        return $this->updateMethod($request, $id, $restDto);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_PATCH])]
    public function patchAction(Request $request, string $id, RestDtoInterface $restDto, User $loggedInUser): Response
    {
        $this->assertOwnedEvent($id, $loggedInUser);

        if ($restDto instanceof Event) {
            $restDto->setUser($loggedInUser);
        }

        return $this->patchMethod($request, $id, $restDto);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_DELETE])]
    public function deleteAction(Request $request, string $id, User $loggedInUser): Response
    {
        $this->assertOwnedEvent($id, $loggedInUser);

        return $this->deleteMethod($request, $id);
    }

    /** @param array<int|string, string|array<mixed>> $criteria */
    public function processCriteria(array &$criteria, Request $request, string $method): void
    {
        if ($this->currentUserId !== null) {
            $criteria['user.id'] = $this->currentUserId;
        }
    }

    private function assertOwnedEvent(string $id, User $loggedInUser): void
    {
        $event = $this->resource->findOne($id, true);

        if (!$event instanceof EventEntity || $event->getUser()?->getId() !== $loggedInUser->getId()) {
            throw new NotFoundHttpException('Event not found.');
        }
    }
}
