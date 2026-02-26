<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Domain\Enum;

use App\Task\Domain\Enum\TaskStatus;
use PHPUnit\Framework\TestCase;

class TaskStatusTest extends TestCase
{
    public function testTodoTransitions(): void
    {
        self::assertFalse(TaskStatus::TODO->canTransitionTo(TaskStatus::TODO));
        self::assertTrue(TaskStatus::TODO->canTransitionTo(TaskStatus::IN_PROGRESS));
        self::assertTrue(TaskStatus::TODO->canTransitionTo(TaskStatus::DONE));
        self::assertTrue(TaskStatus::TODO->canTransitionTo(TaskStatus::ARCHIVED));
    }

    public function testInProgressTransitions(): void
    {
        self::assertFalse(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::TODO));
        self::assertTrue(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::DONE));
        self::assertTrue(TaskStatus::IN_PROGRESS->canTransitionTo(TaskStatus::ARCHIVED));
    }

    public function testDoneTransitions(): void
    {
        self::assertFalse(TaskStatus::DONE->canTransitionTo(TaskStatus::TODO));
        self::assertFalse(TaskStatus::DONE->canTransitionTo(TaskStatus::IN_PROGRESS));
        self::assertFalse(TaskStatus::DONE->canTransitionTo(TaskStatus::DONE));
        self::assertTrue(TaskStatus::DONE->canTransitionTo(TaskStatus::ARCHIVED));
    }

    public function testArchivedTransitions(): void
    {
        self::assertFalse(TaskStatus::ARCHIVED->canTransitionTo(TaskStatus::TODO));
        self::assertFalse(TaskStatus::ARCHIVED->canTransitionTo(TaskStatus::IN_PROGRESS));
        self::assertFalse(TaskStatus::ARCHIVED->canTransitionTo(TaskStatus::DONE));
        self::assertFalse(TaskStatus::ARCHIVED->canTransitionTo(TaskStatus::ARCHIVED));
    }
}
