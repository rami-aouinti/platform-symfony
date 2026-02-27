<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Domain\Entity\Sprint;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * AssignTaskRequestSprint.
 *
 * @package App\Task\Application\UseCase
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class AssignTaskRequestSprint
{
    public function __construct(
        private readonly TaskRequestRepositoryInterface $taskRequestRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly AssertTaskRequestReviewAccess $assertTaskRequestReviewAccess,
    ) {
    }

    public function execute(string $id, ?string $sprintId): TaskRequest
    {
        $request = $this->getRequestById($id);
        $this->assertTaskRequestReviewAccess->execute($request);

        if ($sprintId === null || $sprintId === '') {
            $request->setSprint(null);
            $this->taskRequestRepository->save($request);

            return $request;
        }

        $sprint = $this->entityManager->find(Sprint::class, $sprintId);

        if (!$sprint instanceof Sprint) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Sprint not found.');
        }

        $request->setSprint($sprint);
        $this->taskRequestRepository->save($request);

        return $request;
    }

    private function getRequestById(string $id): TaskRequest
    {
        $entity = $this->taskRequestRepository->find($id);

        if (!$entity instanceof TaskRequest) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Task request not found.');
        }

        return $entity;
    }
}
