<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Domain\Enum;

use App\Task\Domain\Enum\TaskRequestStatus;
use PHPUnit\Framework\TestCase;

class TaskRequestStatusTest extends TestCase
{
    public function testPendingCanTransitionToFinalStatuses(): void
    {
        self::assertFalse(TaskRequestStatus::PENDING->canTransitionTo(TaskRequestStatus::PENDING));
        self::assertTrue(TaskRequestStatus::PENDING->canTransitionTo(TaskRequestStatus::APPROVED));
        self::assertTrue(TaskRequestStatus::PENDING->canTransitionTo(TaskRequestStatus::REJECTED));
        self::assertTrue(TaskRequestStatus::PENDING->canTransitionTo(TaskRequestStatus::CANCELLED));
    }

    public function testFinalStatusesCannotTransition(): void
    {
        foreach ([TaskRequestStatus::APPROVED, TaskRequestStatus::REJECTED, TaskRequestStatus::CANCELLED] as $status) {
            self::assertFalse($status->canTransitionTo(TaskRequestStatus::PENDING));
            self::assertFalse($status->canTransitionTo(TaskRequestStatus::APPROVED));
            self::assertFalse($status->canTransitionTo(TaskRequestStatus::REJECTED));
            self::assertFalse($status->canTransitionTo(TaskRequestStatus::CANCELLED));
        }
    }
}
