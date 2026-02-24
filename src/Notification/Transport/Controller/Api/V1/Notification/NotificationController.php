<?php

declare(strict_types=1);

namespace App\Notification\Transport\Controller\Api\V1\Notification;

use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\Notification\Domain\Entity\Notification;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsController]
#[Route(path: '/v1/notifications')]
#[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
#[OA\Tag(name: 'Notification Management')]
readonly class NotificationController
{
    public function __construct(
        private NotificationServiceInterface $notificationService,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(path: '', methods: [Request::METHOD_GET])]
    public function findAction(User $loggedInUser): JsonResponse
    {
        $notifications = $this->notificationService->findByUser($loggedInUser);

        return $this->serializeNotificationResponse($notifications);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(
        path: '/{id}',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_GET],
    )]
    #[OA\Response(
        response: 404,
        description: 'Notification not found',
        content: new JsonContent(
            properties: [
                new Property(property: 'code', type: 'integer'),
                new Property(property: 'message', type: 'string'),
            ],
            type: 'object',
        ),
    )]
    public function findOneAction(string $id, User $loggedInUser): JsonResponse
    {
        $notification = $this->notificationService->findOneByUser($id, $loggedInUser);

        if (!$notification instanceof Notification) {
            return new JsonResponse([
                'code' => 404,
                'message' => 'Notification not found',
            ], 404);
        }

        return $this->serializeNotificationResponse($notification);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(
        path: '/{id}/read',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_PATCH],
    )]
    public function markAsReadAction(string $id, User $loggedInUser): JsonResponse
    {
        $notification = $this->notificationService->markAsRead($id, $loggedInUser);

        if (!$notification instanceof Notification) {
            return new JsonResponse([
                'code' => 404,
                'message' => 'Notification not found',
            ], 404);
        }

        return $this->serializeNotificationResponse($notification);
    }

    #[Route(path: '/read-all', methods: [Request::METHOD_PATCH])]
    public function markAllAsReadAction(User $loggedInUser): JsonResponse
    {
        $updated = $this->notificationService->markAllAsRead($loggedInUser);

        return new JsonResponse([
            'updated' => $updated,
        ]);
    }

    #[Route(path: '/unread-count', methods: [Request::METHOD_GET])]
    public function unreadCountAction(User $loggedInUser): JsonResponse
    {
        return new JsonResponse([
            'unread' => $this->notificationService->countUnread($loggedInUser),
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    private function serializeNotificationResponse(array|Notification $payload): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($payload, 'json', [
                'groups' => [
                    'Notification',
                    'Notification.id',
                    'Notification.title',
                    'Notification.message',
                    'Notification.type',
                    'Notification.readAt',
                ],
            ]),
            json: true,
        );
    }
}
