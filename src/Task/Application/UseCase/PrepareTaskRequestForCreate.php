<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\TaskRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class PrepareTaskRequestForCreate
{
    public function __construct(
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
    }

    public function execute(TaskRequest $request): void
    {
        $user = $this->currentTaskUserProvider->getCurrentUser();
        $task = $request->getTask();

        if ($task === null) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Task is required.');
        }

        if (!$this->taskAccessService->canViewTask($user, $task)) {
            throw new AccessDeniedHttpException('You cannot create requests for this task.');
        }

        if ($request->getRequestedStatus() === null) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Requested status is required.');
        }

        $request->setRequester($user);
    }
}
