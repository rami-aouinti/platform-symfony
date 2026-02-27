<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\AbstractOwnedResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\Resource\Interfaces\TaskResourceInterface;
use App\Task\Application\UseCase\AssertTaskManageAccess;
use App\Task\Application\UseCase\ChangeTaskStatus;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\PrepareTaskForCreate;
use App\Task\Domain\Entity\Task as Entity;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Exception\InvalidTaskStatusTransition;
use App\Task\Domain\Repository\Interfaces\TaskRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 * @package App\Task\Application\Resource
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class TaskResource extends AbstractOwnedResource implements TaskResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        UserTypeIdentification $userTypeIdentification,
        private readonly TaskAccessServiceInterface $taskAccessService,
        private readonly AssertTaskManageAccess $assertTaskManageAccess,
        private readonly PrepareTaskForCreate $prepareTaskForCreate,
        private readonly ChangeTaskStatus $changeTaskStatus,
    ) {
        parent::__construct($repository, $userTypeIdentification);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $this->taskAccessService->scopeTasksQuery($this->getCurrentUserOrDeny(), $criteria);
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskManageAccess->execute($entity);
        }
    }

    protected function onBeforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->prepareTaskForCreate->execute($entity);
        }
    }

    protected function authorizeBeforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskManageAccess->execute($entity);
        }
    }

    protected function authorizeBeforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskManageAccess->execute($entity);
        }
    }

    protected function authorizeBeforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertTaskManageAccess->execute($entity);
        }
    }

    public function changeStatus(string $id, TaskStatus $status): Entity
    {
        try {
            return $this->changeTaskStatus->execute($id, $status);
        } catch (InvalidTaskStatusTransition $exception) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $exception->getMessage(), $exception);
        }
    }
}
