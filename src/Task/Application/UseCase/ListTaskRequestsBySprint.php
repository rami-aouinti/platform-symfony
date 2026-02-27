<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use function array_values;

final class ListTaskRequestsBySprint
{
    public function __construct(
        private readonly TaskRequestRepositoryInterface $taskRequestRepository,
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
        private readonly TaskAccessServiceInterface $taskAccessService,
        private readonly AssertTaskRequestViewAccess $assertTaskRequestViewAccess,
    ) {
    }

    /**
     * @return array<int|string, mixed>
     */
    public function execute(string $sprintId, ?string $userId = null): array
    {
        $user = $this->currentTaskUserProvider->getCurrentUser();
        $sprintUuid = $this->parseUuid($sprintId, 'sprintId');
        $qb = $this->taskRequestRepository->createQueryBuilder('tr')
            ->leftJoin('tr.task', 't')
            ->leftJoin('tr.sprint', 's')
            ->andWhere('s.id = :sprintId')
            ->setParameter('sprintId', $sprintUuid, UuidBinaryOrderedTimeType::NAME)
            ->orderBy('t.title', 'ASC')
            ->addOrderBy('tr.time', 'ASC');

        if ($userId !== null && $userId !== '') {
            $userUuid = $this->parseUuid($userId, 'user');
            $qb
                ->leftJoin('tr.requester', 'requester')
                ->leftJoin('tr.reviewer', 'reviewer')
                ->andWhere('requester.id = :userId OR reviewer.id = :userId')
                ->setParameter('userId', $userUuid, UuidBinaryOrderedTimeType::NAME);
        }

        /** @var array<int, TaskRequest> $requests */
        $requests = $qb->getQuery()->getResult();

        $grouped = [];

        foreach ($requests as $request) {
            $this->assertTaskRequestViewAccess->execute($request);

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

    private function parseUuid(string $value, string $fieldName): UuidInterface
    {
        try {
            return Uuid::fromString($value);
        } catch (InvalidUuidStringException) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, sprintf('Invalid UUID format for "%s".', $fieldName));
        }
    }
}
