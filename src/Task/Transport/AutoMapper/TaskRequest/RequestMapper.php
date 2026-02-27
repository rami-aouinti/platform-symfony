<?php

declare(strict_types=1);

namespace App\Task\Transport\AutoMapper\TaskRequest;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\Task\Application\Resource\TaskResource;
use App\Task\Domain\Entity\Sprint;
use App\Task\Domain\Entity\Task;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * RequestMapper.
 *
 * @package App\Task\Transport\AutoMapper\TaskRequest
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class RequestMapper extends RestRequestMapper
{
    protected static array $properties = [
        'task',
        'sprint',
        'type',
        'requestedStatus',
        'time',
        'note',
    ];

    public function __construct(
        private readonly TaskResource $taskResource,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    protected function transformTask(?string $task): ?Task
    {
        if ($task === null || $task === '') {
            return null;
        }

        try {
            return $this->taskResource->getReference($task);
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformSprint(?string $sprint): ?Sprint
    {
        if ($sprint === null || $sprint === '') {
            return null;
        }

        try {
            /** @var Sprint $reference */
            $reference = $this->entityManager->getReference(Sprint::class, $sprint);

            return $reference;
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformTime(?string $time): ?DateTimeImmutable
    {
        return $time !== null && $time !== '' ? new DateTimeImmutable($time) : null;
    }
}
