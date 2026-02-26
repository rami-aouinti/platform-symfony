<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\Resource\Interfaces\TaskResourceInterface;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Task as Entity;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class TaskResource extends RestResource implements TaskResourceInterface
{
    public function __construct(
        RepositoryInterface $repository,
        private readonly UserTypeIdentification $userTypeIdentification,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
        parent::__construct($repository);
    }

    public function beforeFind(array &$criteria, array &$orderBy, ?int &$limit, ?int &$offset, array &$search): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->taskAccessService->isAdminLike($currentUser)) {
            return;
        }

        $criteria['owner'] = $currentUser;
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageTask($entity);
        }
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $entity->setOwner($this->getCurrentUser());
        }
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageTask($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageTask($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanManageTask($entity);
        }
    }

    public function changeStatus(string $id, TaskStatus $status): Entity
    {
        $task = $this->getRepository()->find($id);

        if (!$task instanceof Entity) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Task not found.');
        }

        $this->assertCanManageTask($task);
        $task->setStatus($status);
        $this->save($task);

        return $task;
    }

    private function assertCanManageTask(Entity $task): void
    {
        $currentUser = $this->getCurrentUser();

        if ($this->taskAccessService->canManageTask($currentUser, $task)) {
            return;
        }

        throw new AccessDeniedHttpException('Only task owner can manage this task.');
    }

    private function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
