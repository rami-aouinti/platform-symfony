<?php

declare(strict_types=1);

namespace App\JobOffer\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\JobOffer\Domain\Entity\City;
use App\JobOffer\Domain\Entity\JobCategory;
use App\JobOffer\Domain\Entity\JobOffer;
use App\JobOffer\Domain\Entity\Language;
use App\JobOffer\Domain\Entity\Region;
use App\JobOffer\Domain\Entity\Skill;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadJobOfferData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var Company $acme */
        $acme = $this->getReference('Company-acme-demo', Company::class);
        /** @var Company $external */
        $external = $this->getReference('Company-external-corp', Company::class);
        /** @var Company $beta */
        $beta = $this->getReference('Company-beta-labs', Company::class);

        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);

        /** @var City $paris */
        $paris = $this->getReference('City-paris', City::class);
        /** @var City $berlin */
        $berlin = $this->getReference('City-berlin', City::class);
        /** @var City $lyon */
        $lyon = $this->getReference('City-lyon', City::class);
        /** @var City $toulouse */
        $toulouse = $this->getReference('City-toulouse', City::class);

        /** @var Region $idf */
        $idf = $this->getReference('Region-idf', Region::class);
        /** @var Region $berlinRegion */
        $berlinRegion = $this->getReference('Region-berlin', Region::class);
        /** @var Region $ara */
        $ara = $this->getReference('Region-ara', Region::class);
        /** @var Region $occitanie */
        $occitanie = $this->getReference('Region-occitanie', Region::class);

        /** @var Skill $php */
        $php = $this->getReference('Skill-php', Skill::class);
        /** @var Skill $symfony */
        $symfony = $this->getReference('Skill-symfony', Skill::class);
        /** @var Skill $devops */
        $devops = $this->getReference('Skill-devops', Skill::class);
        /** @var Skill $aws */
        $aws = $this->getReference('Skill-aws', Skill::class);
        /** @var Skill $react */
        $react = $this->getReference('Skill-react', Skill::class);
        /** @var Skill $sql */
        $sql = $this->getReference('Skill-sql', Skill::class);

        /** @var Language $fr */
        $fr = $this->getReference('Language-fr', Language::class);
        /** @var Language $en */
        $en = $this->getReference('Language-en', Language::class);
        /** @var Language $de */
        $de = $this->getReference('Language-de', Language::class);

        /** @var JobCategory $backend */
        $backend = $this->getReference('JobCategory-backend', JobCategory::class);
        /** @var JobCategory $platform */
        $platform = $this->getReference('JobCategory-platform', JobCategory::class);
        /** @var JobCategory $dataCategory */
        $dataCategory = $this->getReference('JobCategory-data', JobCategory::class);
        /** @var JobCategory $frontend */
        $frontend = $this->getReference('JobCategory-frontend', JobCategory::class);

        $jobs = [
            [
                'reference' => 'JobOffer-php-backend-engineer',
                'uuid' => '60000000-0000-1000-8000-000000000001',
                'title' => 'Senior PHP Backend Engineer',
                'description' => 'Design and maintain scalable Symfony services.',
                'location' => 'Paris, France',
                'employmentType' => 'full-time',
                'status' => 'open',
                'company' => $acme,
                'createdBy' => $john,
                'salaryMin' => 65000,
                'salaryMax' => 85000,
                'salaryCurrency' => 'EUR',
                'salaryPeriod' => 'yearly',
                'remotePolicy' => 'hybrid',
                'experienceLevel' => 'senior',
                'workTime' => 'full-time',
                'applicationType' => 'internal',
                'publishedAt' => new DateTimeImmutable('2026-06-01 09:00:00'),
                'city' => $paris,
                'region' => $idf,
                'jobCategory' => $backend,
                'skills' => [$php, $symfony],
                'languages' => [$fr, $en],
                'country' => 'FR',
                'languageLevel' => 'fluent',
            ],
            [
                'reference' => 'JobOffer-platform-sre',
                'uuid' => '60000000-0000-1000-8000-000000000002',
                'title' => 'Platform SRE',
                'description' => 'Improve monitoring, incident response and reliability.',
                'location' => 'Remote EU',
                'employmentType' => 'full-time',
                'status' => 'open',
                'company' => $acme,
                'createdBy' => $alice,
                'salaryMin' => 70000,
                'salaryMax' => 92000,
                'salaryCurrency' => 'EUR',
                'salaryPeriod' => 'yearly',
                'remotePolicy' => 'remote',
                'experienceLevel' => 'lead',
                'workTime' => 'full-time',
                'applicationType' => 'external-link',
                'publishedAt' => new DateTimeImmutable('2026-05-15 10:30:00'),
                'city' => $berlin,
                'region' => $berlinRegion,
                'jobCategory' => $platform,
                'skills' => [$devops, $aws],
                'languages' => [$en, $de],
                'country' => 'DE',
                'languageLevel' => 'advanced',
            ],
            [
                'reference' => 'JobOffer-data-engineer-external',
                'uuid' => '60000000-0000-1000-8000-000000000003',
                'title' => 'Data Engineer',
                'description' => 'Build ETL pipelines and lakehouse transformations.',
                'location' => 'Lyon, France',
                'employmentType' => 'contract',
                'status' => 'closed',
                'company' => $external,
                'createdBy' => $carol,
                'salaryMin' => 500,
                'salaryMax' => 700,
                'salaryCurrency' => 'EUR',
                'salaryPeriod' => 'daily',
                'remotePolicy' => 'on-site',
                'experienceLevel' => 'mid',
                'workTime' => 'full-time',
                'applicationType' => 'email',
                'publishedAt' => new DateTimeImmutable('2026-03-20 08:15:00'),
                'city' => $lyon,
                'region' => $ara,
                'jobCategory' => $dataCategory,
                'skills' => [$sql, $php],
                'languages' => [$fr],
                'country' => 'FR',
                'languageLevel' => 'intermediate',
            ],
            [
                'reference' => 'JobOffer-junior-frontend-beta',
                'uuid' => '60000000-0000-1000-8000-000000000004',
                'title' => 'Junior Frontend Engineer',
                'description' => 'Help ship UX improvements and tests.',
                'location' => 'Toulouse, France',
                'employmentType' => 'internship',
                'status' => 'draft',
                'company' => $beta,
                'createdBy' => $alice,
                'salaryMin' => 1200,
                'salaryMax' => 1500,
                'salaryCurrency' => 'EUR',
                'salaryPeriod' => 'monthly',
                'remotePolicy' => 'hybrid',
                'experienceLevel' => 'junior',
                'workTime' => 'part-time',
                'applicationType' => 'internal',
                'publishedAt' => null,
                'city' => $toulouse,
                'region' => $occitanie,
                'jobCategory' => $frontend,
                'skills' => [$react],
                'languages' => [$fr],
                'country' => 'FR',
                'languageLevel' => 'basic',
            ],
        ];

        foreach ($jobs as $data) {
            $job = (new JobOffer())
                ->setTitle($data['title'])
                ->setDescription($data['description'])
                ->setLocation($data['location'])
                ->setEmploymentType($data['employmentType'])
                ->setStatus($data['status'])
                ->setCompany($data['company'])
                ->setCreatedBy($data['createdBy'])
                ->setSalaryMin($data['salaryMin'])
                ->setSalaryMax($data['salaryMax'])
                ->setSalaryCurrency($data['salaryCurrency'])
                ->setSalaryPeriod($data['salaryPeriod'])
                ->setRemotePolicy($data['remotePolicy'])
                ->setExperienceLevel($data['experienceLevel'])
                ->setWorkTime($data['workTime'])
                ->setApplicationType($data['applicationType'])
                ->setPublishedAt($data['publishedAt'])
                ->setCity($data['city'])
                ->setRegion($data['region'])
                ->setJobCategory($data['jobCategory'])
                ->setCountry($data['country'])
                ->setLanguageLevel($data['languageLevel'])
                ->setSkills($data['skills'])
                ->setLanguages($data['languages']);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $job);
            $manager->persist($job);
            $this->addReference($data['reference'], $job);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 7;
    }
}
