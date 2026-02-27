<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\DataFixtures\ORM;

use App\Blog\Domain\Entity\BlogComment;
use App\Blog\Domain\Entity\BlogPost;
use App\Blog\Domain\Enum\BlogReferenceType;
use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Entity\TaskRequest;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * LoadBlogCommentData.
 *
 * @package App\Blog\Infrastructure\DataFixtures\ORM
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class LoadBlogCommentData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);

        /** @var BlogPost $weeklyPost */
        $weeklyPost = $this->getReference('BlogPost-platform-weekly-sync', BlogPost::class);
        /** @var BlogPost $hardeningPost */
        $hardeningPost = $this->getReference('BlogPost-task-hardening-plan', BlogPost::class);

        /** @var Task $task */
        $task = $this->getReference('Task-platform-auth-refactor', Task::class);
        /** @var TaskRequest $taskRequest */
        $taskRequest = $this->getReference('TaskRequest-001', TaskRequest::class);

        $rows = [
            ['BlogComment-001', '75000000-0000-1000-8000-000000000001', $weeklyPost, $john, 'Bonne visibilité sur l avancement task.', BlogReferenceType::TASK, $task->getId()],
            ['BlogComment-002', '75000000-0000-1000-8000-000000000002', $hardeningPost, $alice, 'Ajouter un paragraphe sur les transitions de status.', BlogReferenceType::TASK_REQUEST, $taskRequest->getId()],
        ];

        foreach ($rows as [$reference, $uuid, $post, $author, $content, $referenceType, $referenceId]) {
            $comment = (new BlogComment())
                ->setPost($post)
                ->setAuthor($author)
                ->setContent($content)
                ->setReferenceType($referenceType)
                ->setReferenceId(UuidHelper::fromString($referenceId));

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $comment);
            $manager->persist($comment);
            $this->addReference($reference, $comment);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 14;
    }
}
