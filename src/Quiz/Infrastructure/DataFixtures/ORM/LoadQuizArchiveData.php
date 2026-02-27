<?php

declare(strict_types=1);

namespace App\Quiz\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Quiz\Domain\Entity\Quiz;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * LoadQuizArchiveData.
 *
 * @package App\Quiz\Infrastructure\DataFixtures\ORM
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class LoadQuizArchiveData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);

        $rows = [
            ['Quiz-history-legacy', '76000000-0000-1000-8000-000000000009', 'Legacy History Challenge', 'Historique des produits et milestones internes.', 'history', 'hard', 840, false, null, null, $john],
            ['Quiz-sports-season-review', '76000000-0000-1000-8000-000000000010', 'Sports Season Review', 'Bilan de saison des compétitions majeures.', 'sports', 'medium', 500, false, '2026-05-01 09:00:00', '2026-08-31 18:00:00', $alice],
            ['Quiz-science-lab-basics', '76000000-0000-1000-8000-000000000011', 'Lab Basics', 'Sécurité laboratoire et protocoles.', 'science', 'easy', 300, true, '2026-11-01 09:00:00', '2027-02-28 18:00:00', $john],
        ];

        foreach ($rows as [$reference, $uuid, $title, $description, $category, $difficulty, $timeLimit, $isPublished, $startsAt, $endsAt, $owner]) {
            $quiz = (new Quiz())
                ->setTitle($title)
                ->setDescription($description)
                ->setCategory($category)
                ->setDifficulty($difficulty)
                ->setTimeLimit($timeLimit)
                ->setIsPublished($isPublished)
                ->setStartsAt($startsAt !== null ? new DateTimeImmutable($startsAt) : null)
                ->setEndsAt($endsAt !== null ? new DateTimeImmutable($endsAt) : null)
                ->setOwner($owner);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $quiz);
            $manager->persist($quiz);
            $this->addReference($reference, $quiz);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 16;
    }
}
