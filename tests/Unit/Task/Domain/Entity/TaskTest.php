<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Domain\Entity;

use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Exception\InvalidTaskStatusTransition;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testSetStatusSetsCompletedAtWhenTaskIsDone(): void
    {
        $task = new Task();

        $task->setStatus(TaskStatus::DONE);

        self::assertSame(TaskStatus::DONE, $task->getStatus());
        self::assertNotNull($task->getCompletedAt());
    }

    public function testSetStatusClearsCompletedAtWhenLeavingDoneStatus(): void
    {
        $task = new Task();
        $task->setStatus(TaskStatus::DONE);

        self::assertNotNull($task->getCompletedAt());

        $task->setStatus(TaskStatus::ARCHIVED);

        self::assertSame(TaskStatus::ARCHIVED, $task->getStatus());
        self::assertNull($task->getCompletedAt());
    }

    public function testSetStatusThrowsExceptionForInvalidTransition(): void
    {
        $task = new Task();
        $task->setStatus(TaskStatus::DONE);

        $this->expectException(InvalidTaskStatusTransition::class);

        $task->setStatus(TaskStatus::IN_PROGRESS);
    }
}
