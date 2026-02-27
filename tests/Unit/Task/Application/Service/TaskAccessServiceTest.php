<?php

declare(strict_types=1);

namespace App\Tests\Unit\Task\Application\Service;

use App\Task\Application\Service\TaskAccessService;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskAccessServiceTest extends TestCase
{
    public function testScopeTasksQueryAddsOwnerForNonAdmin(): void
    {
        $service = new TaskAccessService();
        $user = (new User())->setEmail('user@example.com');
        $criteria = [];

        $service->scopeTasksQuery($user, $criteria);

        self::assertArrayHasKey('owner', $criteria);
        self::assertSame($user, $criteria['owner']);
    }

    public function testScopeTaskRequestsQueryDoesNotFilterForAdmin(): void
    {
        $service = new TaskAccessService();
        $admin = (new User())->setEmail('admin@example.com')->setRoles(['ROLE_ADMIN']);
        $criteria = ['existing' => 'value'];

        $service->scopeTaskRequestsQuery($admin, $criteria);

        self::assertSame(['existing' => 'value'], $criteria);
    }

    public function testCanViewTaskRequestAllowsReviewer(): void
    {
        $service = new TaskAccessService();
        $reviewer = (new User())->setEmail('reviewer@example.com');

        $request = (new TaskRequest())->setReviewer($reviewer);

        self::assertTrue($service->canViewTaskRequest($reviewer, $request));
    }

    public function testCanReviewTaskRequestAllowsReviewer(): void
    {
        $service = new TaskAccessService();
        $reviewer = (new User())->setEmail('reviewer@example.com');

        $request = (new TaskRequest())->setReviewer($reviewer);

        self::assertTrue($service->canReviewTaskRequest($reviewer, $request));
    }

    public function testCanManageTaskAllowsProjectOwner(): void
    {
        $service = new TaskAccessService();
        $projectOwner = (new User())->setEmail('owner@example.com');

        $project = (new Project())->setOwner($projectOwner);
        $task = (new Task())->setProject($project);

        self::assertTrue($service->canManageTask($projectOwner, $task));
    }
}
