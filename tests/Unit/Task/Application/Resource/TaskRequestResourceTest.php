<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Application\Resource;

use App\Task\Application\Resource\TaskRequestResource;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use App\User\Application\Resource\UserResource;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskRequestResourceTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testChangeRequestedStatusUpdatesRequest(): void
    {
        $manager = (new User())
            ->setFirstName('John')
            ->setLastName('Manager')
            ->setUsername('john.manager')
            ->setEmail('john.manager@example.com');

        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setRequestedStatus(TaskStatus::TODO);

        $repository = $this->createMock(TaskRequestRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with('task-request-id')
            ->willReturn($taskRequest);
        $repository
            ->expects($this->once())
            ->method('save')
            ->with($taskRequest, true, null)
            ->willReturnSelf();

        $userTypeIdentification = $this->createMock(UserTypeIdentification::class);
        $userTypeIdentification
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($manager);

        $accessService = $this->createMock(TaskAccessServiceInterface::class);
        $accessService
            ->expects($this->once())
            ->method('canReviewTaskRequest')
            ->with($manager, $taskRequest)
            ->willReturn(true);

        $resource = new TaskRequestResource(
            $repository,
            $userTypeIdentification,
            $accessService,
            $this->createMock(UserResource::class),
            $this->createMock(EntityManagerInterface::class),
        );

        $result = $resource->changeRequestedStatus('task-request-id', TaskStatus::DONE);

        self::assertSame($taskRequest, $result);
        self::assertSame(TaskStatus::DONE, $taskRequest->getRequestedStatus());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testChangeRequestedStatusDeniedForNonReviewer(): void
    {
        $manager = (new User())
            ->setFirstName('John')
            ->setLastName('Manager')
            ->setUsername('john.manager')
            ->setEmail('john.manager@example.com');

        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setRequestedStatus(TaskStatus::TODO);

        $repository = $this->createMock(TaskRequestRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->willReturn($taskRequest);
        $repository
            ->expects($this->never())
            ->method('save');

        $userTypeIdentification = $this->createMock(UserTypeIdentification::class);
        $userTypeIdentification
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($manager);

        $accessService = $this->createMock(TaskAccessServiceInterface::class);
        $accessService
            ->expects($this->once())
            ->method('canReviewTaskRequest')
            ->willReturn(false);

        $resource = new TaskRequestResource(
            $repository,
            $userTypeIdentification,
            $accessService,
            $this->createMock(UserResource::class),
            $this->createMock(EntityManagerInterface::class),
        );

        $this->expectException(AccessDeniedHttpException::class);
        $resource->changeRequestedStatus('task-request-id', TaskStatus::DONE);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testListBySprintGroupedByTaskThrowsBadRequestForInvalidSprintId(): void
    {
        $manager = (new User())
            ->setFirstName('John')
            ->setLastName('Manager')
            ->setUsername('john.manager')
            ->setEmail('john.manager@example.com');

        $repository = $this->createMock(TaskRequestRepositoryInterface::class);
        $repository
            ->expects($this->never())
            ->method('createQueryBuilder');

        $userTypeIdentification = $this->createMock(UserTypeIdentification::class);
        $userTypeIdentification
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($manager);

        $resource = new TaskRequestResource(
            $repository,
            $userTypeIdentification,
            $this->createMock(TaskAccessServiceInterface::class),
            $this->createMock(UserResource::class),
            $this->createMock(EntityManagerInterface::class),
        );

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Invalid UUID format for "sprintId".');

        $resource->listBySprintGroupedByTask('73000000-0000-1000-8000-00000000000');
    }
}
