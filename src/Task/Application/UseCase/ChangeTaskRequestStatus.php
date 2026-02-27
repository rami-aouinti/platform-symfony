<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskStatus;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * ChangeTaskRequestStatus.
 *
 * @package App\Task\Application\UseCase
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class ChangeTaskRequestStatus
{
    public function __construct(
        private readonly TaskRequestRepositoryInterface $taskRequestRepository,
        private readonly AssertTaskRequestReviewAccess $assertTaskRequestReviewAccess,
    ) {
    }

    public function execute(string $id, TaskStatus $requestedStatus): TaskRequest
    {
        $request = $this->getRequestById($id);
        $this->assertTaskRequestReviewAccess->execute($request);

        $request->setRequestedStatus($requestedStatus);
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
