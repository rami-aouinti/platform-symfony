<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Domain\Enum;

use App\Task\Domain\Enum\ProjectStatus;
use PHPUnit\Framework\TestCase;

class ProjectStatusTest extends TestCase
{
    public function testStatusTransition(): void
    {
        self::assertTrue(ProjectStatus::ACTIVE->canTransitionTo(ProjectStatus::ARCHIVED));
        self::assertFalse(ProjectStatus::ACTIVE->canTransitionTo(ProjectStatus::ACTIVE));
        self::assertTrue(ProjectStatus::ARCHIVED->canTransitionTo(ProjectStatus::ACTIVE));
    }
}
