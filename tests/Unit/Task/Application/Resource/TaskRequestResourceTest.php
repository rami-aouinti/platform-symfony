<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\Task\Application\Resource\TaskRequestResource;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\Service\TaskAccessService;
use App\Task\Application\UseCase\AssertTaskRequestReviewAccess;
use App\Task\Application\UseCase\AssertTaskRequestViewAccess;
use App\Task\Application\UseCase\AssignTaskRequestRequester;
use App\Task\Application\UseCase\AssignTaskRequestReviewer;
use App\Task\Application\UseCase\AssignTaskRequestSprint;
use App\Task\Application\UseCase\ChangeTaskRequestStatus;
use App\Task\Application\UseCase\ListTaskRequestsBySprint;
use App\Task\Application\UseCase\PrepareTaskRequestForCreate;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TaskRequestResourceTest extends TestCase
{
    public function testBeforePatchDeniesRequesterWithViewOnlyAccess(): void
    {
        $requester = (new User())->setEmail('requester@example.com');

        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setRequester($requester);

        $resource = $this->createResourceForUser($requester, new TaskAccessService());
        $id = 'task-request-id';

        $this->expectException(AccessDeniedHttpException::class);
        $resource->beforePatch($id, $this->createMock(RestDtoInterface::class), $taskRequest);
    }

    public function testBeforeUpdateAllowsReviewer(): void
    {
        $reviewer = (new User())->setEmail('reviewer@example.com');

        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setReviewer($reviewer);

        $resource = $this->createResourceForUser($reviewer, new TaskAccessService());
        $id = 'task-request-id';

        $resource->beforeUpdate($id, $this->createMock(RestDtoInterface::class), $taskRequest);

        self::assertTrue(true);
    }

    public function testBeforePatchAllowsAdmin(): void
    {
        $admin = (new User())
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_ADMIN']);

        $taskRequest = (new TaskRequest())
            ->setTask(new Task())
            ->setRequester((new User())->setEmail('requester@example.com'));

        $resource = $this->createResourceForUser($admin, new TaskAccessService());
        $id = 'task-request-id';

        $resource->beforePatch($id, $this->createMock(RestDtoInterface::class), $taskRequest);

        self::assertTrue(true);
    }

    private function createResourceForUser(User $user, TaskAccessServiceInterface $accessService): TaskRequestResource
    {
        $currentTaskUserProvider = $this->createMock(CurrentTaskUserProvider::class);
        $currentTaskUserProvider
            ->method('getCurrentUser')
            ->willReturn($user);

        return new TaskRequestResource(
            $this->createMock(TaskRequestRepositoryInterface::class),
            $currentTaskUserProvider,
            $accessService,
            $this->createMock(PrepareTaskRequestForCreate::class),
            new AssertTaskRequestViewAccess($currentTaskUserProvider, $accessService),
            new AssertTaskRequestReviewAccess($currentTaskUserProvider, $accessService),
            $this->createMock(ChangeTaskRequestStatus::class),
            $this->createMock(ListTaskRequestsBySprint::class),
            $this->createMock(AssignTaskRequestRequester::class),
            $this->createMock(AssignTaskRequestReviewer::class),
            $this->createMock(AssignTaskRequestSprint::class),
        );
    }
}
