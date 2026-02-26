<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\DataFixtures\ORM;

use App\Blog\Domain\Entity\BlogPost;
use App\Blog\Domain\Entity\BlogTag;
use App\Blog\Domain\Enum\BlogPostStatus;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadBlogPostData extends Fixture implements OrderedFixtureInterface
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

        /** @var BlogTag $platform */
        $platform = $this->getReference('BlogTag-platform', BlogTag::class);
        /** @var BlogTag $taskUpdates */
        $taskUpdates = $this->getReference('BlogTag-task-updates', BlogTag::class);
        /** @var BlogTag $releaseNotes */
        $releaseNotes = $this->getReference('BlogTag-release-notes', BlogTag::class);
        /** @var BlogTag $security */
        $security = $this->getReference('BlogTag-security', BlogTag::class);

        $rows = [
            ['BlogPost-platform-weekly-sync', '74000000-0000-1000-8000-000000000001', 'Platform weekly sync', 'platform-weekly-sync', 'Etat hebdomadaire de la plateforme.', 'Résumé des changements, incidents et métriques.', BlogPostStatus::PUBLISHED, $john, [$platform, $taskUpdates]],
            ['BlogPost-task-hardening-plan', '74000000-0000-1000-8000-000000000002', 'Task hardening plan', 'task-hardening-plan', 'Plan de robustesse task/taskrequest.', 'Décrit les étapes de hardening et la roadmap.', BlogPostStatus::DRAFT, $alice, [$taskUpdates, $security]],
            ['BlogPost-release-june', '74000000-0000-1000-8000-000000000003', 'Release June', 'release-june', 'Release note de juin.', 'Nouveautés, correctifs et migration DB.', BlogPostStatus::PUBLISHED, $carol, [$releaseNotes, $platform]],
        ];

        foreach ($rows as [$reference, $uuid, $title, $slug, $excerpt, $content, $status, $owner, $tags]) {
            $post = (new BlogPost())
                ->setTitle($title)
                ->setSlug($slug)
                ->setExcerpt($excerpt)
                ->setContent($content)
                ->setStatus($status)
                ->setOwner($owner);

            foreach ($tags as $tag) {
                $post->addTag($tag);
            }

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $post);
            $manager->persist($post);
            $this->addReference($reference, $post);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 13;
    }
}
