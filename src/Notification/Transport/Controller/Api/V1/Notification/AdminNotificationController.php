<?php

declare(strict_types=1);

namespace App\Notification\Transport\Controller\Api\V1\Notification;

use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\User\Domain\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route(path: '/api/v1/admin/notifications')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ROOT')"))]
#[OA\Tag(name: 'Notification Management')]
readonly class AdminNotificationController
{
    public function __construct(private NotificationServiceInterface $notificationService)
    {
    }

    #[Route(
        path: '/users/{id}/unread-count',
        requirements: [
            'id' => Requirement::UUID_V1,
        ],
        methods: [Request::METHOD_GET],
    )]
    public function unreadCountByUserAction(User $user): JsonResponse
    {
        return new JsonResponse([
            'unread' => $this->notificationService->countUnread($user),
        ]);
    }
}
