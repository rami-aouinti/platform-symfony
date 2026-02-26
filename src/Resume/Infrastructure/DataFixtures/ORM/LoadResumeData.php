<?php

declare(strict_types=1);

namespace App\Resume\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Resume\Domain\Entity\Resume;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Resume
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class LoadResumeData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $bob */
        $bob = $this->getReference('User-bob-admin', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);

        $bobPublicResume = (new Resume())
            ->setOwner($bob)
            ->setTitle('Bob - Public Resume')
            ->setSummary('Public profile for Bob.')
            ->setExperiences([[
                'company' => 'Acme',
                'role' => 'Backend Engineer',
            ]])
            ->setEducation([[
                'school' => 'Tech University',
                'degree' => 'MSc Computer Science',
            ]])
            ->setSkills(['PHP', 'Symfony'])
            ->setLinks([[
                'label' => 'LinkedIn',
                'url' => 'https://example.test/bob-linkedin',
            ]])
            ->setIsPublic(true);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('60000000-0000-1000-8000-000000000001'), $bobPublicResume);

        $bobPrivateResume = (new Resume())
            ->setOwner($bob)
            ->setTitle('Bob - Private Resume')
            ->setSummary('Private profile for Bob.')
            ->setExperiences([[
                'company' => 'Globex',
                'role' => 'Staff Engineer',
            ]])
            ->setEducation([[
                'school' => 'Engineering School',
                'degree' => 'BSc Software Engineering',
            ]])
            ->setSkills(['Architecture', 'DDD'])
            ->setLinks([[
                'label' => 'Portfolio',
                'url' => 'https://example.test/bob-portfolio',
            ]])
            ->setIsPublic(false);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('60000000-0000-1000-8000-000000000002'), $bobPrivateResume);

        $carolPrivateResume = (new Resume())
            ->setOwner($carol)
            ->setTitle('Carol - Private Resume')
            ->setSummary('Private profile for Carol.')
            ->setExperiences([[
                'company' => 'Initech',
                'role' => 'SRE',
            ]])
            ->setEducation([[
                'school' => 'State University',
                'degree' => 'BSc Information Systems',
            ]])
            ->setSkills(['Kubernetes', 'Observability'])
            ->setLinks([[
                'label' => 'GitHub',
                'url' => 'https://example.test/carol-github',
            ]])
            ->setIsPublic(false);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('60000000-0000-1000-8000-000000000003'), $carolPrivateResume);

        foreach ([$bobPublicResume, $bobPrivateResume, $carolPrivateResume] as $resume) {
            $manager->persist($resume);
        }

        $manager->flush();

        $this->addReference('Resume-bob-public', $bobPublicResume);
        $this->addReference('Resume-bob-private', $bobPrivateResume);
        $this->addReference('Resume-carol-private', $carolPrivateResume);
    }

    #[Override]
    public function getOrder(): int
    {
        return 7;
    }
}
