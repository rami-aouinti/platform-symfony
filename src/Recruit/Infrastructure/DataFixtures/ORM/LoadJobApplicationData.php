<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Recruit\Domain\Entity\JobApplication;
use App\Recruit\Domain\Entity\JobOffer;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\JobApplication
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class LoadJobApplicationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var JobOffer $phpOffer */
        $phpOffer = $this->getReference('JobOffer-php-backend-engineer', JobOffer::class);
        /** @var JobOffer $sreOffer */
        $sreOffer = $this->getReference('JobOffer-platform-sre', JobOffer::class);
        /** @var JobOffer $dataOffer */
        $dataOffer = $this->getReference('JobOffer-data-engineer-external', JobOffer::class);

        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);
        /** @var User $hugo */
        $hugo = $this->getReference('User-hugo-user', User::class);

        $carolPending = (new JobApplication())
            ->setJobOffer($phpOffer)
            ->setCandidate($carol)
            ->setCoverLetter('I built high-scale Symfony APIs and event-driven systems for 5 years.')
            ->setCvUrl('https://cdn.example.test/cv/carol.pdf')
            ->setAttachments(['https://cdn.example.test/portfolio/carol.pdf'])
            ->setStatus(JobApplicationStatus::PENDING);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('50000000-0000-1000-8000-000000000001'), $carolPending);

        $hugoAccepted = (new JobApplication())
            ->setJobOffer($phpOffer)
            ->setCandidate($hugo)
            ->setCoverLetter('I can maintain and scale critical APIs with clean architecture.')
            ->setCvUrl('https://cdn.example.test/cv/hugo.pdf')
            ->setAttachments(['https://cdn.example.test/github/hugo'])
            ->setStatus(JobApplicationStatus::ACCEPTED)
            ->setDecidedBy($john)
            ->setDecidedAt(new DateTimeImmutable('2026-02-18 09:30:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('50000000-0000-1000-8000-000000000002'), $hugoAccepted);

        $carolRejected = (new JobApplication())
            ->setJobOffer($sreOffer)
            ->setCandidate($carol)
            ->setCoverLetter('I improved SLO processes and postmortems in production teams.')
            ->setStatus(JobApplicationStatus::REJECTED)
            ->setDecidedBy($alice)
            ->setDecidedAt(new DateTimeImmutable('2026-02-20 13:45:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('50000000-0000-1000-8000-000000000003'), $carolRejected);

        $hugoWithdrawn = (new JobApplication())
            ->setJobOffer($dataOffer)
            ->setCandidate($hugo)
            ->setCoverLetter('I have shipped modern ELT stacks with dbt and Airflow.')
            ->setStatus(JobApplicationStatus::WITHDRAWN);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('50000000-0000-1000-8000-000000000004'), $hugoWithdrawn);

        foreach ([$carolPending, $hugoAccepted, $carolRejected, $hugoWithdrawn] as $application) {
            $manager->persist($application);
        }

        $manager->flush();

        $this->addReference('JobApplication-carol-on-php-backend-engineer', $carolPending);
        $this->addReference('JobApplication-hugo-accepted-on-php-backend-engineer', $hugoAccepted);
        $this->addReference('JobApplication-carol-rejected-on-platform-sre', $carolRejected);
        $this->addReference('JobApplication-hugo-withdrawn-on-data-engineer', $hugoWithdrawn);
    }

    #[Override]
    public function getOrder(): int
    {
        return 8;
    }
}
