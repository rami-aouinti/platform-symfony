<?php

declare(strict_types=1);

namespace App\Notification\Transport\Controller\Api\V1\Notification;

use App\General\Transport\Rest\Controller;
use App\General\Transport\Rest\ResponseHandler;
use App\Notification\Application\Resource\Interfaces\NotificationResourceInterface;
use App\Notification\Application\Resource\NotificationResource;
use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method NotificationResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
#[AsController]
#[Route(path: '/v1/notifications')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Notification Management')]
class NotificationController extends Controller
{
    public function __construct(
        NotificationResourceInterface $resource,
        private readonly NotificationServiceInterface $notificationService,
    ) {
        parent::__construct($resource);
    }

    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function findAction(Request $request, User $loggedInUser): Response
    {
        $filters = [
            'read' => $request->query->has('read') ? $request->query->getBoolean('read') : null,
            'type' => $request->query->get('type'),
            'search' => $request->query->get('search'),
            'limit' => $request->query->has('limit') ? $request->query->getInt('limit') : null,
            'offset' => $request->query->has('offset') ? $request->query->getInt('offset') : null,
        ];

        return $this->getResponseHandler()->createResponse(
            $request,
            $this->notificationService->listForUser($loggedInUser, $filters),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_GET])]
    public function findOneAction(Request $request, string $id, User $loggedInUser): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->notificationService->getForUser($id, $loggedInUser),
            $this->getResource(),
        );
    }

    #[Route(path: '/{id}/read', requirements: ['id' => Requirement::UUID_V1], methods: [Request::METHOD_PATCH])]
    public function markAsReadAction(Request $request, string $id, User $loggedInUser): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            $this->notificationService->markAsRead($id, $loggedInUser),
            $this->getResource(),
        );
    }

    #[Route(path: '/read-all', methods: [Request::METHOD_PATCH])]
    public function markAllAsReadAction(Request $request, User $loggedInUser): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            ['updated' => $this->notificationService->markAllAsRead($loggedInUser)],
        );
    }

    #[Route(path: '/unread-count', methods: [Request::METHOD_GET])]
    public function unreadCountAction(Request $request, User $loggedInUser): Response
    {
        return $this->getResponseHandler()->createResponse(
            $request,
            ['count' => $this->notificationService->countUnread($loggedInUser)],
        );
    }
}
