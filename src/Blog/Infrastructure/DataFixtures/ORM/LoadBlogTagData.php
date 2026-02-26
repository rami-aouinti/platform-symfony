<?php

declare(strict_types=1);

namespace App\Blog\Infrastructure\DataFixtures\ORM;

use App\Blog\Domain\Entity\BlogTag;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadBlogTagData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['BlogTag-release-notes', '73000000-0000-1000-8000-000000000001', 'Release Notes', 'release-notes'],
            ['BlogTag-task-updates', '73000000-0000-1000-8000-000000000002', 'Task Updates', 'task-updates'],
            ['BlogTag-platform', '73000000-0000-1000-8000-000000000003', 'Platform', 'platform'],
            ['BlogTag-security', '73000000-0000-1000-8000-000000000004', 'Security', 'security'],
        ];

        foreach ($rows as [$reference, $uuid, $name, $slug]) {
            $tag = (new BlogTag())
                ->setName($name)
                ->setSlug($slug);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $tag);
            $manager->persist($tag);
            $this->addReference($reference, $tag);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 12;
    }
}
