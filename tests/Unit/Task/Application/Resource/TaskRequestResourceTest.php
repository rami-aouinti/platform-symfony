<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Application\Resource;

use App\Task\Application\Resource\TaskRequestResource;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskRequestStatus;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TaskRequestResourceTest extends TestCase
{
    #[AllowMockObjectsWithoutExpectations]
    public function testChangeRequestedStatusUpdatesPendingRequest(): void
    {
        $manager = (new User())
            ->setFirstName('John')
            ->setLastName('Manager')
            ->setUsername('john.manager')
            ->setEmail('john.manager@example.com');

        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setStatus(TaskRequestStatus::PENDING)
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

        $resource = new TaskRequestResource($repository, $userTypeIdentification, $accessService);

        $result = $resource->changeRequestedStatus('task-request-id', TaskStatus::DONE);

        self::assertSame($taskRequest, $result);
        self::assertSame(TaskStatus::DONE, $taskRequest->getRequestedStatus());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testChangeRequestedStatusRejectsNonPendingRequest(): void
    {
        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setStatus(TaskRequestStatus::APPROVED)
            ->setRequestedStatus(TaskStatus::TODO);

        $repository = $this->createMock(TaskRequestRepositoryInterface::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with('task-request-id')
            ->willReturn($taskRequest);
        $repository
            ->expects($this->never())
            ->method('save');

        $resource = new TaskRequestResource(
            $repository,
            $this->createMock(UserTypeIdentification::class),
            $this->createMock(TaskAccessServiceInterface::class),
        );

        try {
            $resource->changeRequestedStatus('task-request-id', TaskStatus::DONE);
            self::fail('Expected HttpException to be thrown.');
        } catch (HttpException $exception) {
            self::assertSame(Response::HTTP_BAD_REQUEST, $exception->getStatusCode());
        }
    }
}
