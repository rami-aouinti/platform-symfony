<?php

declare(strict_types=1);

namespace App\JobOffer\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\JobOffer\Domain\Entity\JobOffer;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
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
            ['JobOffer-php-backend-engineer', '60000000-0000-1000-8000-000000000001', 'Senior PHP Backend Engineer', 'Design and maintain scalable Symfony services.', 'Paris, France', 'full-time', 'open', $acme, $john],
            ['JobOffer-platform-sre', '60000000-0000-1000-8000-000000000002', 'Platform SRE', 'Improve monitoring, incident response and reliability.', 'Remote EU', 'full-time', 'open', $acme, $alice],
            ['JobOffer-data-engineer-external', '60000000-0000-1000-8000-000000000003', 'Data Engineer', 'Build ETL pipelines and lakehouse transformations.', 'Lyon, France', 'contract', 'closed', $external, $carol],
            ['JobOffer-junior-frontend-beta', '60000000-0000-1000-8000-000000000004', 'Junior Frontend Engineer', 'Help ship UX improvements and tests.', 'Toulouse, France', 'internship', 'draft', $beta, $alice],
        ];

        foreach ($jobs as [$reference, $uuid, $title, $description, $location, $employmentType, $status, $company, $createdBy]) {
            $job = (new JobOffer())
                ->setTitle($title)
                ->setDescription($description)
                ->setLocation($location)
                ->setEmploymentType($employmentType)
                ->setStatus($status)
                ->setCompany($company)
                ->setCreatedBy($createdBy);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $job);
            $manager->persist($job);
            $this->addReference($reference, $job);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 6;
    }
}
