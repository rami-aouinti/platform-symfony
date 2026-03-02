<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Recruit\Domain\Entity\Resume;
use App\Recruit\Domain\Entity\ResumeEducation;
use App\Recruit\Domain\Entity\ResumeExperience;
use App\Recruit\Domain\Entity\ResumeLanguage;
use App\Recruit\Domain\Entity\ResumeProject;
use App\Recruit\Domain\Entity\ResumeReference;
use App\Recruit\Domain\Entity\ResumeSkill;
use App\Recruit\Domain\Enum\ResumeEducationLevel;
use App\Recruit\Domain\Enum\ResumeEmploymentType;
use App\Recruit\Domain\Enum\ResumeSkillLevel;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * LoadResumeData.
 *
 * @package App\Recruit\Infrastructure\DataFixtures\ORM
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class LoadResumeData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $john */
        $john = $this->getReference('User-john-root', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);

        $johnPublicResume = (new Resume())
            ->setOwner($john)
            ->setTitle('John - Public Resume')
            ->setSummary('Public profile for John.')
            ->setIsPublic(true);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('60000000-0000-1000-8000-000000000001'), $johnPublicResume);

        $johnPrivateResume = (new Resume())
            ->setOwner($john)
            ->setTitle('John - Private Resume')
            ->setSummary('Private profile for John.')
            ->setIsPublic(false);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('60000000-0000-1000-8000-000000000002'), $johnPrivateResume);

        $carolPrivateResume = (new Resume())
            ->setOwner($carol)
            ->setTitle('Carol - Private Resume')
            ->setSummary('Private profile for Carol.')
            ->setIsPublic(false);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('60000000-0000-1000-8000-000000000003'), $carolPrivateResume);

        foreach ([$johnPublicResume, $johnPrivateResume, $carolPrivateResume] as $resume) {
            $manager->persist($resume);
        }

        $resumeEntries = [
            (new ResumeExperience())
                ->setResume($johnPublicResume)
                ->setTitle('Backend Engineer')
                ->setCompanyName('Acme')
                ->setEmploymentType(ResumeEmploymentType::FULL_TIME)
                ->setStartDate(new DateTimeImmutable('2021-01-01'))
                ->setIsCurrent(true)
                ->setDescription('Build and maintain Symfony APIs.')
                ->setSortOrder(1),
            (new ResumeEducation())
                ->setResume($johnPublicResume)
                ->setSchoolName('Tech University')
                ->setDegree('MSc Computer Science')
                ->setLevel(ResumeEducationLevel::MASTER)
                ->setStartDate(new DateTimeImmutable('2017-09-01'))
                ->setEndDate(new DateTimeImmutable('2019-06-30'))
                ->setSortOrder(1),
            (new ResumeSkill())
                ->setResume($johnPublicResume)
                ->setName('Symfony')
                ->setLevel(ResumeSkillLevel::ADVANCED)
                ->setYearsExperience(6)
                ->setSortOrder(1),
            (new ResumeReference())
                ->setResume($johnPublicResume)
                ->setName('Alice Martin')
                ->setRelationName('Engineering Manager')
                ->setContactEmail('alice@example.test')
                ->setSortOrder(1),
            (new ResumeProject())
                ->setResume($johnPublicResume)
                ->setName('Recruitment Platform')
                ->setDescription('End-to-end recruiting workflow application.')
                ->setProjectUrl('https://example.test/projects/recruitment-platform')
                ->setStartDate(new DateTimeImmutable('2022-03-01'))
                ->setSortOrder(1),
            (new ResumeLanguage())
                ->setResume($johnPublicResume)
                ->setName('French')
                ->setLevel('Native')
                ->setSortOrder(1),

            (new ResumeExperience())
                ->setResume($johnPrivateResume)
                ->setTitle('Staff Engineer')
                ->setCompanyName('Globex')
                ->setEmploymentType(ResumeEmploymentType::FULL_TIME)
                ->setStartDate(new DateTimeImmutable('2020-05-01'))
                ->setIsCurrent(true)
                ->setSortOrder(1),
            (new ResumeEducation())
                ->setResume($johnPrivateResume)
                ->setSchoolName('Engineering School')
                ->setDegree('BSc Software Engineering')
                ->setLevel(ResumeEducationLevel::BACHELOR)
                ->setStartDate(new DateTimeImmutable('2013-09-01'))
                ->setEndDate(new DateTimeImmutable('2016-06-30'))
                ->setSortOrder(1),
            (new ResumeSkill())
                ->setResume($johnPrivateResume)
                ->setName('DDD')
                ->setLevel(ResumeSkillLevel::EXPERT)
                ->setYearsExperience(8)
                ->setSortOrder(1),
            (new ResumeReference())
                ->setResume($johnPrivateResume)
                ->setName('David Ross')
                ->setRelationName('CTO')
                ->setContactEmail('david@example.test')
                ->setSortOrder(1),
            (new ResumeProject())
                ->setResume($johnPrivateResume)
                ->setName('Legacy Modernization')
                ->setDescription('Refactoring monolith into modular architecture.')
                ->setSortOrder(1),
            (new ResumeLanguage())
                ->setResume($johnPrivateResume)
                ->setName('English')
                ->setLevel('Professional')
                ->setSortOrder(1),

            (new ResumeExperience())
                ->setResume($carolPrivateResume)
                ->setTitle('SRE')
                ->setCompanyName('Initech')
                ->setEmploymentType(ResumeEmploymentType::FULL_TIME)
                ->setStartDate(new DateTimeImmutable('2019-11-01'))
                ->setIsCurrent(true)
                ->setSortOrder(1),
            (new ResumeEducation())
                ->setResume($carolPrivateResume)
                ->setSchoolName('State University')
                ->setDegree('BSc Information Systems')
                ->setLevel(ResumeEducationLevel::BACHELOR)
                ->setStartDate(new DateTimeImmutable('2012-09-01'))
                ->setEndDate(new DateTimeImmutable('2015-06-30'))
                ->setSortOrder(1),
            (new ResumeSkill())
                ->setResume($carolPrivateResume)
                ->setName('Kubernetes')
                ->setLevel(ResumeSkillLevel::ADVANCED)
                ->setYearsExperience(5)
                ->setSortOrder(1),
            (new ResumeReference())
                ->setResume($carolPrivateResume)
                ->setName('Nora Hill')
                ->setRelationName('Platform Lead')
                ->setContactEmail('nora@example.test')
                ->setSortOrder(1),
            (new ResumeProject())
                ->setResume($carolPrivateResume)
                ->setName('Observability Stack')
                ->setDescription('Centralized monitoring and alerting platform.')
                ->setSortOrder(1),
            (new ResumeLanguage())
                ->setResume($carolPrivateResume)
                ->setName('English')
                ->setLevel('Fluent')
                ->setSortOrder(1),
        ];

        foreach ($resumeEntries as $entry) {
            $manager->persist($entry);
        }

        $manager->flush();

        $this->addReference('Resume-john-root-public', $johnPublicResume);
        $this->addReference('Resume-john-root-private', $johnPrivateResume);
        $this->addReference('Resume-carol-private', $carolPrivateResume);
    }

    #[Override]
    public function getOrder(): int
    {
        return 7;
    }
}
