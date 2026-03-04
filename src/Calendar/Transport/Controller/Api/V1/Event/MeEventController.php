<?php

declare(strict_types=1);

namespace App\Calendar\Transport\Controller\Api\V1\Event;

use App\Calendar\Application\DTO\Event\Event;
use App\Calendar\Application\Resource\Interfaces\EventResourceInterface;
use App\Calendar\Domain\Entity\Event as EventEntity;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\CrudController;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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

    private ?User $currentUser = null;

    public function __construct(
        EventResourceInterface $resource,
        private readonly UserTypeIdentification $userTypeIdentification,
    )
    {
        parent::__construct($resource);
    }

    /** @throws Throwable */
    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function findAction(Request $request): Response
    {
        $loggedInUser = $this->getCurrentUserOrDeny();
        $this->currentUser = $loggedInUser;

        return $this->findMethod($request);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_GET])]
    public function findOneAction(Request $request, string $id): Response
    {
        $loggedInUser = $this->getCurrentUserOrDeny();
        $this->assertOwnedEvent($id, $loggedInUser);

        return $this->findOneMethod($request, $id);
    }

    /** @throws Throwable */
    #[Route(path: '', methods: [Request::METHOD_POST])]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        $loggedInUser = $this->getCurrentUserOrDeny();

        if ($restDto instanceof Event) {
            $restDto->setUser($loggedInUser);
        }

        return $this->createMethod($request, $restDto);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_PUT])]
    public function updateAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        $loggedInUser = $this->getCurrentUserOrDeny();
        $this->assertOwnedEvent($id, $loggedInUser);

        if ($restDto instanceof Event) {
            $restDto->setUser($loggedInUser);
        }

        return $this->updateMethod(request: $request, restDto: $restDto, id: $id);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_PATCH])]
    #[OA\Patch(
        summary: 'Modifier partiellement un événement du calendrier de l’utilisateur connecté',
        security: [[
            'Bearer' => [],
        ], [
            'ApiKey' => [],
        ]],
    )]
    #[OA\RequestBody(
        request: 'body',
        description: 'Payload de mise à jour partielle de l’événement',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                'startAt' => '2026-03-05T17:28:00+01:00',
                'endAt' => '2026-03-05T19:28:00+01:00',
                'isAllDay' => false,
            ],
        ),
    )]
    #[OA\Response(response: 200, description: 'Événement partiellement modifié')]
    #[OA\Response(response: 401, ref: '#/components/responses/UnauthorizedError')]
    #[OA\Response(response: 403, ref: '#/components/responses/ForbiddenError')]
    #[OA\Response(response: 404, ref: '#/components/responses/NotFoundError')]
    public function patchAction(Request $request, RestDtoInterface $restDto, string $id): Response
    {
        $loggedInUser = $this->getCurrentUserOrDeny();
        $this->assertOwnedEvent($id, $loggedInUser);

        if ($restDto instanceof Event) {
            $restDto->setUser($loggedInUser);
        }

        return $this->patchMethod(request: $request, restDto: $restDto, id: $id);
    }

    /** @throws Throwable */
    #[Route(path: '/{id}', methods: [Request::METHOD_DELETE])]
    public function deleteAction(Request $request, string $id): Response
    {
        $loggedInUser = $this->getCurrentUserOrDeny();
        $this->assertOwnedEvent($id, $loggedInUser);

        return $this->deleteMethod($request, $id);
    }

    /** @param array<int|string, mixed> $criteria */
    public function processCriteria(array &$criteria, Request $request, string $method): void
    {
        if ($this->currentUser !== null) {
            unset($criteria['user']);
            $criteria['and'][] = ['IDENTITY(entity.user)', 'eq', $this->currentUser->getId()];
        }
    }

    private function assertOwnedEvent(string $id, User $loggedInUser): void
    {
        $event = $this->resource->findOne($id, true);

        if (!$event instanceof EventEntity || $event->getUser()?->getId() !== $loggedInUser->getId()) {
            throw new NotFoundHttpException('Event not found.');
        }
    }

    private function getCurrentUserOrDeny(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
