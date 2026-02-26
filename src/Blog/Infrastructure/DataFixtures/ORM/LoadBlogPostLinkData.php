<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\DataFixtures\ORM;

use App\Blog\Domain\Entity\BlogPost;
use App\Blog\Domain\Entity\BlogPostLink;
use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadBlogPostLinkData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var BlogPost $weeklyPost */
        $weeklyPost = $this->getReference('BlogPost-platform-weekly-sync', BlogPost::class);
        /** @var BlogPost $hardeningPost */
        $hardeningPost = $this->getReference('BlogPost-task-hardening-plan', BlogPost::class);

        /** @var Task $task */
        $task = $this->getReference('Task-platform-auth-refactor', Task::class);
        /** @var TaskRequest $taskRequest */
        $taskRequest = $this->getReference('TaskRequest-001', TaskRequest::class);

        $rows = [
            ['BlogPostLink-task-001', '76000000-0000-1000-8000-000000000001', $weeklyPost, $task, null, BlogReferenceType::TASK],
            ['BlogPostLink-task-request-001', '76000000-0000-1000-8000-000000000002', $hardeningPost, null, $taskRequest, BlogReferenceType::TASK_REQUEST],
        ];

        foreach ($rows as [$reference, $uuid, $post, $taskEntity, $taskRequestEntity, $referenceType]) {
            $link = (new BlogPostLink())
                ->setPost($post)
                ->setTask($taskEntity)
                ->setTaskRequest($taskRequestEntity)
                ->setReferenceType($referenceType);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $link);
            $manager->persist($link);
            $this->addReference($reference, $link);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 15;
    }
}
