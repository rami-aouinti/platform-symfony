<?php

declare(strict_types=1);

namespace App\Notification\Domain\Repository\Interfaces;

use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use App\Notification\Domain\Entity\Notification;
use App\User\Domain\Entity\User;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array<string, mixed> $filters
     *
     * @return array<int, Notification>
     */
    public function findByUser(User $user, array $filters = []): array;

    public function findOneByIdAndUser(string $id, User $user): ?Notification;

    public function markAllAsReadForUser(User $user): int;

    public function countUnreadForUser(User $user): int;
}
