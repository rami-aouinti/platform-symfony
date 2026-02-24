<?php

declare(strict_types=1);

namespace App\Notification\Application\Service;

use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notificationRepository,
    ) {
    }

    public function listForUser(User $user, array $filters = []): array
    {
        return $this->notificationRepository->findByUser($user, $filters);
    }

    public function getForUser(string $id, User $user): Notification
    {
        $notification = $this->notificationRepository->findOneByIdAndUser($id, $user);

        if (!$notification instanceof Notification) {
            throw new NotFoundHttpException('Notification not found.');
        }

        return $notification;
    }

    public function markAsRead(string $id, User $user): Notification
    {
        $notification = $this->getForUser($id, $user);

        if (!$notification->isRead()) {
            $notification->setReadAt(new \DateTime());
            $this->notificationRepository->save($notification);
        }

        return $notification;
    }

    public function markAllAsRead(User $user): int
    {
        return $this->notificationRepository->markAllAsReadForUser($user);
    }

    public function countUnread(User $user): int
    {
        return $this->notificationRepository->countUnreadForUser($user);
    }
}
