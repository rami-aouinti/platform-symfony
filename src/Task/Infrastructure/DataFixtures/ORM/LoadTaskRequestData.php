<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Sprint;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Task\Domain\Enum\TaskRequestType;
use App\Task\Domain\Enum\TaskStatus;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
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
            ['TaskRequest-001', '72000000-0000-1000-8000-000000000001', 'Task-platform-auth-refactor', 'Sprint-2026-09-01', $alice, $john, TaskStatus::DONE, '2026-09-04 09:00:00', 'Can we mark this done after QA?'],
            ['TaskRequest-002', '72000000-0000-1000-8000-000000000002', 'Task-platform-cache-tags', 'Sprint-2026-09-01', $alice, $john, TaskStatus::IN_PROGRESS, '2026-09-05 10:30:00', 'Started implementation.'],
            ['TaskRequest-003', '72000000-0000-1000-8000-000000000003', 'Task-hiring-candidate-search', 'Sprint-2026-09-01', $john, $alice, TaskStatus::DONE, '2026-09-06 15:20:00', 'Need product validation first.'],
            ['TaskRequest-004', '72000000-0000-1000-8000-000000000004', 'Task-hiring-application-timeline', 'Sprint-2026-09-01', $john, $alice, TaskStatus::IN_PROGRESS, '2026-09-07 11:00:00', 'Cancelled, duplicate request.'],
            ['TaskRequest-005', '72000000-0000-1000-8000-000000000005', 'Task-design-datatable', 'Sprint-2026-09-15', $carol, $alice, TaskStatus::IN_PROGRESS, '2026-09-15 08:40:00', 'Moving this to in progress.'],
            ['TaskRequest-006', '72000000-0000-1000-8000-000000000006', 'Task-design-theme-tokens', 'Sprint-2026-09-15', $john, $alice, TaskStatus::DONE, '2026-09-16 09:10:00', 'Tokens fully integrated.'],
            ['TaskRequest-007', '72000000-0000-1000-8000-000000000007', 'Task-b2b-contract-upload', 'Sprint-2026-09-15', $alice, $carol, TaskStatus::DONE, '2026-09-16 14:00:00', 'Upload done, please review security checks.'],
            ['TaskRequest-008', '72000000-0000-1000-8000-000000000008', 'Task-b2b-account-switch', 'Sprint-2026-09-15', $john, $carol, TaskStatus::IN_PROGRESS, '2026-09-17 16:25:00', 'Development started with feature flag.'],
            ['TaskRequest-009', '72000000-0000-1000-8000-000000000009', 'Task-observability-tracing', 'Sprint-2026-09-15', $alice, $john, TaskStatus::DONE, '2026-09-18 13:15:00', 'Missing traces in queue workers.'],
            ['TaskRequest-010', '72000000-0000-1000-8000-000000000010', 'Task-observability-slo', 'Sprint-2026-09-15', $carol, $john, TaskStatus::IN_PROGRESS, '2026-09-18 09:45:00', 'Please approve start.'],
            ['TaskRequest-011', '72000000-0000-1000-8000-000000000011', 'Task-generic-security-1', 'Sprint-2026-09-15', $alice, $john, TaskStatus::DONE, '2026-09-19 10:10:00', 'Security checklist completed.'],
            ['TaskRequest-012', '72000000-0000-1000-8000-000000000012', 'Task-generic-security-2', 'Sprint-2026-09-15', $carol, $alice, TaskStatus::IN_PROGRESS, '2026-09-19 12:00:00', 'Dependency upgrade postponed.'],
            ['TaskRequest-013', '72000000-0000-1000-8000-000000000013', 'Task-generic-ops-1', null, $john, $carol, TaskStatus::ARCHIVED, '2026-09-20 08:30:00', 'Task can be archived.'],
            ['TaskRequest-014', '72000000-0000-1000-8000-000000000014', 'Task-generic-doc-1', null, $carol, $alice, TaskStatus::ARCHIVED, '2026-09-20 11:30:00', 'Documentation task complete and archived.'],
            ['TaskRequest-015', '72000000-0000-1000-8000-000000000015', 'Task-generic-backlog-1', null, $alice, $john, TaskStatus::IN_PROGRESS, '2026-09-21 09:00:00', 'Started backlog refinement.'],
        ];

        foreach ($rows as [$reference, $uuid, $taskReference, $sprintReference, $requester, $reviewer, $requestedStatus, $time, $note]) {
            /** @var Task $task */
            $task = $this->getReference($taskReference, Task::class);
            /** @var Sprint|null $sprint */
            $sprint = $sprintReference !== null ? $this->getReference($sprintReference, Sprint::class) : null;

            $request = (new TaskRequest())
                ->setTask($task)
                ->setSprint($sprint)
                ->setRequester($requester)
                ->setReviewer($reviewer)
                ->setType(TaskRequestType::STATUS_CHANGE)
                ->setRequestedStatus($requestedStatus)
                ->setTime(new DateTimeImmutable($time))
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
        return 12;
    }
}
