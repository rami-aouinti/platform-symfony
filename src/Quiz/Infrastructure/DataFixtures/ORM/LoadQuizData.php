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

final class LoadQuizData extends Fixture implements OrderedFixtureInterface
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
            ['Quiz-general-onboarding', '76000000-0000-1000-8000-000000000001', 'Onboarding Essentials', 'Quiz d\'intégration pour les nouveaux collaborateurs.', 'general', 'easy', 300, true, '2026-07-01 09:00:00', '2026-12-31 18:00:00', $john],
            ['Quiz-science-fundamentals', '76000000-0000-1000-8000-000000000002', 'Science Fundamentals', 'Évalue les bases scientifiques générales.', 'science', 'medium', 600, true, '2026-07-10 09:00:00', '2026-12-31 18:00:00', $alice],
            ['Quiz-history-classics', '76000000-0000-1000-8000-000000000003', 'History Classics', 'Questions sur les grandes périodes historiques.', 'history', 'medium', 480, true, '2026-08-01 09:00:00', '2026-12-31 18:00:00', $carol],
            ['Quiz-sports-champions', '76000000-0000-1000-8000-000000000004', 'Sports Champions', 'Culture sportive internationale.', 'sports', 'easy', 420, false, null, null, $john],
            ['Quiz-tech-advanced', '76000000-0000-1000-8000-000000000005', 'Tech Advanced', 'Quiz avancé sur architecture, backend et cloud.', 'technology', 'hard', 900, true, '2026-09-01 09:00:00', '2026-12-31 18:00:00', $alice],
            ['Quiz-entertainment-mix', '76000000-0000-1000-8000-000000000006', 'Entertainment Mix', 'Cinéma, séries et musique populaire.', 'entertainment', 'easy', 360, true, '2026-07-15 09:00:00', '2026-12-31 18:00:00', $carol],
            ['Quiz-general-security', '76000000-0000-1000-8000-000000000007', 'Security Awareness', 'Bonnes pratiques sécurité en entreprise.', 'general', 'medium', 540, false, null, null, $john],
            ['Quiz-tech-api-design', '76000000-0000-1000-8000-000000000008', 'API Design Review', 'Normes d\'API REST et versioning.', 'technology', 'hard', 720, true, '2026-10-01 09:00:00', '2027-01-31 18:00:00', $alice],
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
        return 11;
    }
}
