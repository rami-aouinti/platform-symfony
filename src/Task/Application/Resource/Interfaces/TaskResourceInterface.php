<?php

declare(strict_types=1);

namespace App\Task\Application\Resource\Interfaces;

use App\General\Application\Rest\Interfaces\RestResourceInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enum\TaskStatus;

/**
 * TaskResourceInterface.
 *
 * @package App\Task\Application\Resource\Interfaces
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
interface TaskResourceInterface extends RestResourceInterface
{
    public function changeStatus(string $id, TaskStatus $status): Task;
}
