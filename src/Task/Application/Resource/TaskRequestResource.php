<?php

declare(strict_types=1);

namespace App\Task\Application\Resource;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestResource;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\Task\Application\DTO\TaskRequest\TaskRequest as TaskRequestDto;
use App\Task\Application\Resource\Interfaces\TaskRequestResourceInterface;
use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Domain\Entity\Sprint;
use App\Task\Domain\Entity\TaskRequest as Entity;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface as RepositoryInterface;
use App\User\Application\Resource\UserResource;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
        private readonly UserResource $userResource,
        private readonly EntityManagerInterface $entityManager,
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

        $entity->setRequester($user);
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

    public function changeRequestedStatus(string $id, TaskStatus $requestedStatus): Entity
    {
        $request = $this->getRequestById($id);

        $this->assertCanReviewRequest($request);
        $request->setRequestedStatus($requestedStatus);
        $this->save($request);

        return $request;
    }

    public function listBySprintGroupedByTask(string $sprintId, ?string $userId = null): array
    {
        $user = $this->getCurrentUser();
        $qb = $this->getRepository()->createQueryBuilder('tr')
            ->leftJoin('tr.task', 't')
            ->leftJoin('tr.sprint', 's')
            ->andWhere('s.id = :sprintId')
            ->setParameter('sprintId', $sprintId)
            ->orderBy('t.title', 'ASC')
            ->addOrderBy('tr.time', 'ASC');

        if ($userId !== null && $userId !== '') {
            $qb
                ->leftJoin('tr.requester', 'requester')
                ->leftJoin('tr.reviewer', 'reviewer')
                ->andWhere('requester.id = :userId OR reviewer.id = :userId')
                ->setParameter('userId', $userId);
        }

        /** @var array<int, Entity> $requests */
        $requests = $qb->getQuery()->getResult();

        $grouped = [];

        foreach ($requests as $request) {
            $this->assertCanViewRequest($request);

            $task = $request->getTask();
            $taskId = $task?->getId() ?? 'no-task';

            if (!isset($grouped[$taskId])) {
                $grouped[$taskId] = [
                    'task' => $task,
                    'taskRequests' => [],
                ];
            }

            $grouped[$taskId]['taskRequests'][] = $request;
        }

        if (!$this->taskAccessService->isAdminLike($user) && $userId !== null && $userId !== '' && $user->getId() !== $userId) {
            throw new AccessDeniedHttpException('You cannot filter by another user.');
        }

        return [
            'sprintId' => $sprintId,
            'groupedByTask' => array_values($grouped),
        ];
    }

    public function assignRequester(string $id, string $requesterId): Entity
    {
        $request = $this->getRequestById($id);
        $this->assertCanReviewRequest($request);

        $requester = $this->getUserById($requesterId);
        $request->setRequester($requester);
        $this->save($request);

        return $request;
    }

    public function assignReviewer(string $id, string $reviewerId): Entity
    {
        $request = $this->getRequestById($id);
        $this->assertCanReviewRequest($request);

        $reviewer = $this->getUserById($reviewerId);
        $request->setReviewer($reviewer);
        $this->save($request);

        return $request;
    }

    public function assignSprint(string $id, ?string $sprintId): Entity
    {
        $request = $this->getRequestById($id);
        $this->assertCanReviewRequest($request);

        if ($sprintId === null || $sprintId === '') {
            $request->setSprint(null);
            $this->save($request);

            return $request;
        }

        $sprint = $this->entityManager->find(Sprint::class, $sprintId);

        if (!$sprint instanceof Sprint) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Sprint not found.');
        }

        $request->setSprint($sprint);
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

    private function getUserById(string $id): User
    {
        $user = $this->userResource->findOne($id);

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found.');
        }

        return $user;
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
