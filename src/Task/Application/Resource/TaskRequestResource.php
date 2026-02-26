<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\DTO\TaskRequest\TaskRequest as TaskRequestDto;
use App\Task\Application\Resource\Interfaces\TaskRequestResourceInterface;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\TaskRequest as Entity;
use App\Task\Domain\Enum\TaskRequestStatus;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface as RepositoryInterface;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @method Entity[] find(?array $criteria = null, ?array $orderBy = null, ?int $limit = null, ?int $offset = null, ?array $search = null, ?string $entityManagerName = null)
 */
class TaskRequestResource extends RestResource implements TaskRequestResourceInterface
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
        $user = $this->getCurrentUser();

        if ($this->taskAccessService->isAdminLike($user)) {
            return;
        }

        $criteria['requester'] = $user;
    }

    public function beforeCreate(RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if (!$entity instanceof Entity || !$restDto instanceof TaskRequestDto) {
            return;
        }

        $user = $this->getCurrentUser();
        $task = $entity->getTask();

        if ($task === null) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Task is required.');
        }

        if (!$this->taskAccessService->canViewTask($user, $task)) {
            throw new AccessDeniedHttpException('You cannot create requests for this task.');
        }

        if ($entity->getRequestedStatus() === null) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Requested status is required.');
        }

        $entity
            ->setRequester($user)
            ->setStatus(TaskRequestStatus::PENDING);
    }

    public function beforeUpdate(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanViewRequest($entity);
        }
    }

    public function beforePatch(string &$id, RestDtoInterface $restDto, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanViewRequest($entity);
        }
    }

    public function beforeDelete(string &$id, EntityInterface $entity): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanReviewRequest($entity);
        }
    }

    public function afterFindOne(string &$id, ?EntityInterface $entity = null): void
    {
        if ($entity instanceof Entity) {
            $this->assertCanViewRequest($entity);
        }
    }

    public function approve(string $id): Entity
    {
        $request = $this->getRequestById($id);

        $this->assertCanReviewRequest($request);
        $request->setStatus(TaskRequestStatus::APPROVED);

        if ($request->getRequestedStatus() !== null && $request->getTask() !== null) {
            $request->getTask()->setStatus($request->getRequestedStatus());
        }

        $request->setReviewer($this->getCurrentUser());
        $this->save($request);

        return $request;
    }

    public function reject(string $id): Entity
    {
        $request = $this->getRequestById($id);

        $this->assertCanReviewRequest($request);
        $request
            ->setStatus(TaskRequestStatus::REJECTED)
            ->setReviewer($this->getCurrentUser());

        $this->save($request);

        return $request;
    }

    public function cancel(string $id): Entity
    {
        $request = $this->getRequestById($id);
        $user = $this->getCurrentUser();

        if ($request->getRequester()?->getId() !== $user->getId() && !$this->taskAccessService->isAdminLike($user)) {
            throw new AccessDeniedHttpException('Only requester can cancel this request.');
        }

        $request
            ->setStatus(TaskRequestStatus::CANCELLED)
            ->setReviewer($user);

        $this->save($request);

        return $request;
    }

    private function getRequestById(string $id): Entity
    {
        $entity = $this->getRepository()->find($id);

        if (!$entity instanceof Entity) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Task request not found.');
        }

        return $entity;
    }

    private function assertCanViewRequest(Entity $request): void
    {
        $user = $this->getCurrentUser();

        if ($this->taskAccessService->canViewTaskRequest($user, $request)) {
            return;
        }

        throw new AccessDeniedHttpException('Not allowed to view this request.');
    }

    private function assertCanReviewRequest(Entity $request): void
    {
        $user = $this->getCurrentUser();

        if ($this->taskAccessService->canReviewTaskRequest($user, $request)) {
            return;
        }

        throw new AccessDeniedHttpException('Only task manager can review this request.');
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
