<?php

declare(strict_types=1);

namespace App\Task\Domain\Exception;

use App\Task\Domain\Enum\TaskStatus;
use RuntimeException;

final class InvalidTaskStatusTransition extends RuntimeException
{
    public static function create(TaskStatus $from, TaskStatus $to): self
    {
        return new self(sprintf('Task status transition from "%s" to "%s" is not allowed.', $from->value, $to->value));
    }
}
