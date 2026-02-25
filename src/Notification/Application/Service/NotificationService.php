<?php

declare(strict_types=1);

namespace App\Notification\Application\Service;

use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Repository\Interfaces\NotificationRepositoryInterface;
use App\User\Domain\Entity\User;
use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
readonly class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
    ) {
    }

    public function create(User $user, string $type, string $title, string $message): Notification
    {
        $notification = (new Notification($user))
            ->setType($type)
            ->setTitle($title)
            ->setMessage($message);

        $this->notificationRepository->save($notification);

        return $notification;
    }

    public function findByUser(User $user): array
    {
        return $this->notificationRepository->findBy(
            criteria: [
                'user' => $user->getId(),
            ],
            orderBy: [
                'createdAt' => 'DESC',
            ],
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function findOneByUser(string $id, User $user): ?Notification
    {
        $notification = $this->notificationRepository->find($id);

        if (!$notification instanceof Notification || $notification->getUser()->getId() !== $user->getId()) {
            return null;
        }

        return $notification;
    }

    public function countUnread(User $user): int
    {
        return $this->notificationRepository->countUnreadByUserId($user->getId());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function markAsRead(string $id, User $user): ?Notification
    {
        $notification = $this->findOneByUser($id, $user);

        if (!$notification instanceof Notification) {
            return null;
        }

        $notification->setReadAt(new DateTime());
        $this->notificationRepository->save($notification);

        return $notification;
    }

    public function markAllAsRead(User $user): int
    {
        return $this->notificationRepository->markAllAsReadByUserId($user->getId(), new DateTime());
    }
}
