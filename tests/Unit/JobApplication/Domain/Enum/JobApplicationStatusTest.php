<?php

declare(strict_types=1);

namespace App\Tests\Unit\JobApplication\Domain\Enum;

use App\JobApplication\Domain\Enum\JobApplicationStatus;
use PHPUnit\Framework\TestCase;

class JobApplicationStatusTest extends TestCase
{
    public function testPendingCanTransitionToFinalStatusesOnly(): void
    {
        self::assertFalse(JobApplicationStatus::PENDING->canTransitionTo(JobApplicationStatus::PENDING));
        self::assertTrue(JobApplicationStatus::PENDING->canTransitionTo(JobApplicationStatus::ACCEPTED));
        self::assertTrue(JobApplicationStatus::PENDING->canTransitionTo(JobApplicationStatus::REJECTED));
        self::assertTrue(JobApplicationStatus::PENDING->canTransitionTo(JobApplicationStatus::WITHDRAWN));
    }

    public function testFinalStatusesCannotTransition(): void
    {
        foreach ([JobApplicationStatus::ACCEPTED, JobApplicationStatus::REJECTED, JobApplicationStatus::WITHDRAWN] as $status) {
            self::assertFalse($status->canTransitionTo(JobApplicationStatus::PENDING));
            self::assertFalse($status->canTransitionTo(JobApplicationStatus::ACCEPTED));
            self::assertFalse($status->canTransitionTo(JobApplicationStatus::REJECTED));
            self::assertFalse($status->canTransitionTo(JobApplicationStatus::WITHDRAWN));
        }
    }
}
