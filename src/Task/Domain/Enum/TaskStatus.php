<?php

declare(strict_types=1);

namespace App\Task\Domain\Enum;

use App\General\Domain\Enum\Interfaces\DatabaseEnumInterface;
use App\General\Domain\Enum\Traits\GetValues;

enum TaskStatus: string implements DatabaseEnumInterface
{
    use GetValues;

    case TODO = 'todo';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';
    case ARCHIVED = 'archived';

    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::TODO => $target !== self::TODO,
            self::IN_PROGRESS => $target !== self::TODO,
            self::DONE => $target === self::ARCHIVED,
            self::ARCHIVED => false,
        };
    }
}
