<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskRequestStatus;
use App\Task\Domain\Enum\TaskRequestType;
use App\Task\Domain\Enum\TaskStatus;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadTaskRequestData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);

        $rows = [
            ['TaskRequest-001', '72000000-0000-1000-8000-000000000001', 'Task-platform-auth-refactor', $alice, $john, TaskStatus::DONE, TaskRequestStatus::PENDING, 'Can we mark this done after QA?'],
            ['TaskRequest-002', '72000000-0000-1000-8000-000000000002', 'Task-platform-cache-tags', $alice, $john, TaskStatus::IN_PROGRESS, TaskRequestStatus::APPROVED, 'Started implementation.'],
            ['TaskRequest-003', '72000000-0000-1000-8000-000000000003', 'Task-hiring-candidate-search', $john, $alice, TaskStatus::DONE, TaskRequestStatus::REJECTED, 'Need product validation first.'],
            ['TaskRequest-004', '72000000-0000-1000-8000-000000000004', 'Task-hiring-application-timeline', $john, $alice, TaskStatus::IN_PROGRESS, TaskRequestStatus::CANCELLED, 'Cancelled, duplicate request.'],
            ['TaskRequest-005', '72000000-0000-1000-8000-000000000005', 'Task-design-datatable', $carol, $alice, TaskStatus::IN_PROGRESS, TaskRequestStatus::PENDING, 'Moving this to in progress.'],
            ['TaskRequest-006', '72000000-0000-1000-8000-000000000006', 'Task-design-theme-tokens', $john, $alice, TaskStatus::DONE, TaskRequestStatus::APPROVED, 'Tokens fully integrated.'],
            ['TaskRequest-007', '72000000-0000-1000-8000-000000000007', 'Task-b2b-contract-upload', $alice, $carol, TaskStatus::DONE, TaskRequestStatus::PENDING, 'Upload done, please review security checks.'],
            ['TaskRequest-008', '72000000-0000-1000-8000-000000000008', 'Task-b2b-account-switch', $john, $carol, TaskStatus::IN_PROGRESS, TaskRequestStatus::APPROVED, 'Development started with feature flag.'],
            ['TaskRequest-009', '72000000-0000-1000-8000-000000000009', 'Task-observability-tracing', $alice, $john, TaskStatus::DONE, TaskRequestStatus::REJECTED, 'Missing traces in queue workers.'],
            ['TaskRequest-010', '72000000-0000-1000-8000-000000000010', 'Task-observability-slo', $carol, $john, TaskStatus::IN_PROGRESS, TaskRequestStatus::PENDING, 'Please approve start.'],
            ['TaskRequest-011', '72000000-0000-1000-8000-000000000011', 'Task-generic-security-1', $alice, $john, TaskStatus::DONE, TaskRequestStatus::PENDING, 'Security checklist completed.'],
            ['TaskRequest-012', '72000000-0000-1000-8000-000000000012', 'Task-generic-security-2', $carol, $alice, TaskStatus::IN_PROGRESS, TaskRequestStatus::CANCELLED, 'Dependency upgrade postponed.'],
            ['TaskRequest-013', '72000000-0000-1000-8000-000000000013', 'Task-generic-ops-1', $john, $carol, TaskStatus::ARCHIVED, TaskRequestStatus::APPROVED, 'Task can be archived.'],
            ['TaskRequest-014', '72000000-0000-1000-8000-000000000014', 'Task-generic-doc-1', $carol, $alice, TaskStatus::ARCHIVED, TaskRequestStatus::APPROVED, 'Documentation task complete and archived.'],
            ['TaskRequest-015', '72000000-0000-1000-8000-000000000015', 'Task-generic-backlog-1', $alice, $john, TaskStatus::IN_PROGRESS, TaskRequestStatus::PENDING, 'Started backlog refinement.'],
        ];

        foreach ($rows as [$reference, $uuid, $taskReference, $requester, $reviewer, $requestedStatus, $status, $note]) {
            /** @var Task $task */
            $task = $this->getReference($taskReference, Task::class);

            $request = (new TaskRequest())
                ->setTask($task)
                ->setRequester($requester)
                ->setReviewer($reviewer)
                ->setType(TaskRequestType::STATUS_CHANGE)
                ->setRequestedStatus($requestedStatus)
                ->setStatus($status)
                ->setNote($note);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $request);
            $manager->persist($request);
            $this->addReference($reference, $request);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 11;
    }
}
