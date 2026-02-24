<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Interfaces;

use App\Notification\Domain\Entity\Notification;
use App\User\Domain\Entity\User;

interface NotificationServiceInterface
{
    /**
     * @param array<string, mixed> $filters
     *
     * @return array<int, Notification>
     */
    public function listForUser(User $user, array $filters = []): array;

    public function getForUser(string $id, User $user): Notification;

    public function markAsRead(string $id, User $user): Notification;

    public function markAllAsRead(User $user): int;

    public function countUnread(User $user): int;
}
