<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Interfaces;

use App\Notification\Domain\Entity\Notification;
use App\User\Domain\Entity\User;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface NotificationServiceInterface
{
    /**
     * @return Notification[]
     */
    public function findByUser(User $user): array;

    public function findOneByUser(string $id, User $user): ?Notification;

    public function countUnread(User $user): int;

    public function markAsRead(string $id, User $user): ?Notification;


    public function create(User $user, string $type, string $title, string $message): Notification;

    public function markAllAsRead(User $user): int;
}
