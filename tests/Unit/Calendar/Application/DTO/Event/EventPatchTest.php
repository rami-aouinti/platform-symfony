<?php

declare(strict_types=1);

namespace App\Tests\Unit\Calendar\Application\DTO\Event;

use App\Calendar\Application\DTO\Event\EventPatch;
use PHPUnit\Framework\TestCase;

class EventPatchTest extends TestCase
{
    public function testPatchSupportsIsAllDayProperty(): void
    {
        $target = new EventPatch();
        $patch = (new EventPatch())->setIsAllDay(true);

        $target->patch($patch);

        self::assertTrue($target->isAllDay());
    }
}
