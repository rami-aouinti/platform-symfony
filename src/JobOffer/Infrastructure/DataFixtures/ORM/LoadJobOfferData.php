<?php

declare(strict_types=1);

namespace App\JobOffer\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\JobOffer\Domain\Entity\JobOffer;
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
                'city' => 'Paris',
                'region' => 'Île-de-France',
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
                'city' => 'Berlin',
                'region' => 'Berlin',
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
                'city' => 'Lyon',
                'region' => 'Auvergne-Rhône-Alpes',
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
                'city' => 'Toulouse',
                'region' => 'Occitanie',
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
                ->setCountry($data['country'])
                ->setLanguageLevel($data['languageLevel']);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($data['uuid']), $job);
            $manager->persist($job);
            $this->addReference($data['reference'], $job);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 6;
    }
}
