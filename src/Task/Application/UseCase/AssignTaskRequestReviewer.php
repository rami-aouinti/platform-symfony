<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Repository\Interfaces\TaskRequestRepositoryInterface;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class AssignTaskRequestReviewer
{
    public function __construct(
        private readonly TaskRequestRepositoryInterface $taskRequestRepository,
        private readonly UserResource $userResource,
        private readonly AssertTaskRequestReviewAccess $assertTaskRequestReviewAccess,
    ) {
    }

    public function execute(string $id, string $reviewerId): TaskRequest
    {
        $request = $this->getRequestById($id);
        $this->assertTaskRequestReviewAccess->execute($request);

        $request->setReviewer($this->getUserById($reviewerId));
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

    private function getUserById(string $id): User
    {
        $user = $this->userResource->findOne($id);

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found.');
        }

        return $user;
    }
}
