<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\DTO\TaskRequest\TaskRequest as TaskRequestDto;
use App\Task\Application\Resource\Interfaces\TaskRequestResourceInterface;
use App\Task\Application\UseCase\AssertTaskRequestReviewAccess;
use App\Task\Application\UseCase\AssertTaskRequestViewAccess;
use App\Task\Application\UseCase\AssignTaskRequestRequester;
use App\Task\Application\UseCase\AssignTaskRequestReviewer;
use App\Task\Application\UseCase\AssignTaskRequestSprint;
use App\Task\Application\UseCase\ChangeTaskRequestStatus;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Application\UseCase\ListTaskRequestsBySprint;
use App\Task\Application\UseCase\PrepareTaskRequestForCreate;
use App\Task\Domain\Entity\TaskRequest as Entity;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface as RepositoryInterface;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Task\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class TaskRequestResource extends RestResource implements TaskRequestResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
        private readonly TaskAccessServiceInterface $taskAccessService,
        private readonly PrepareTaskRequestForCreate $prepareTaskRequestForCreate,
        private readonly AssertTaskRequestViewAccess $assertTaskRequestViewAccess,
        private readonly AssertTaskRequestReviewAccess $assertTaskRequestReviewAccess,
        private readonly ChangeTaskRequestStatus $changeTaskRequestStatus,
        private readonly ListTaskRequestsBySprint $listTaskRequestsBySprint,
        private readonly AssignTaskRequestRequester $assignTaskRequestRequester,
        private readonly AssignTaskRequestReviewer $assignTaskRequestReviewer,
        private readonly AssignTaskRequestSprint $assignTaskRequestSprint,
    ) {
        parent::__construct($repository);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $this->taskAccessService->scopeTaskRequestsQuery($this->currentTaskUserProvider->getCurrentUser(), $criteria);
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity || !$restDto instanceof TaskRequestDto) {
            return;
        }

        $this->prepareTaskRequestForCreate->execute($entity);
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskRequestReviewAccess->execute($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskRequestReviewAccess->execute($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskRequestReviewAccess->execute($entity);
        }
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskRequestViewAccess->execute($entity);
        }
    }

    public function changeRequestedStatus(string $id, TaskStatus $requestedStatus): Entity
    {
        return $this->changeTaskRequestStatus->execute($id, $requestedStatus);
    }

    public function listBySprintGroupedByTask(string $sprintId, ?string $userId = null): array
    {
        return $this->listTaskRequestsBySprint->execute($sprintId, $userId);
    }

    public function assignRequester(string $id, string $requesterId): Entity
    {
        return $this->assignTaskRequestRequester->execute($id, $requesterId);
    }

    public function assignReviewer(string $id, string $reviewerId): Entity
    {
        return $this->assignTaskRequestReviewer->execute($id, $reviewerId);
    }

    public function assignSprint(string $id, ?string $sprintId): Entity
    {
        return $this->assignTaskRequestSprint->execute($id, $sprintId);
    }
}
