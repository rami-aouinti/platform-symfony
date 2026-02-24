<?php

declare(strict_types=1);

namespace App\Notification\Domain\Repository\Interfaces;

use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use DateTimeInterface;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function countUnreadByUserId(string $userId): int;

    public function markAllAsReadByUserId(string $userId, DateTimeInterface $readAt): int;
}
