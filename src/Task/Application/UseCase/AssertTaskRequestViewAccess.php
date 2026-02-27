<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase;

use App\Task\Application\Service\Interfaces\TaskAccessServiceInterface;
use App\Task\Application\UseCase\Support\CurrentTaskUserProvider;
use App\Task\Domain\Entity\TaskRequest;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AssertTaskRequestViewAccess
{
    public function __construct(
        private readonly CurrentTaskUserProvider $currentTaskUserProvider,
        private readonly TaskAccessServiceInterface $taskAccessService,
    ) {
    }

    public function execute(TaskRequest $request): void
    {
        $user = $this->currentTaskUserProvider->getCurrentUser();

        if (!$this->taskAccessService->canViewTaskRequest($user, $request)) {
            throw new AccessDeniedHttpException('Not allowed to view this request.');
        }
    }
}
