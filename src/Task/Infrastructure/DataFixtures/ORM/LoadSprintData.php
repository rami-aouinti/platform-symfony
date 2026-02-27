<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Sprint;
use App\Tests\Utils\PhpUnitUtil;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadSprintData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $sprints = [
            ['Sprint-2026-09-01', '73000000-0000-1000-8000-000000000001', '2026-09-01 00:00:00', '2026-09-14 23:59:59', 'Company-acme-demo'],
            ['Sprint-2026-09-15', '73000000-0000-1000-8000-000000000002', '2026-09-15 00:00:00', '2026-09-28 23:59:59', 'Company-acme-demo'],
            ['Sprint-2026-09-29', '73000000-0000-1000-8000-000000000003', '2026-09-29 00:00:00', '2026-10-12 23:59:59', 'Company-external-corp'],
            ['Sprint-2026-10-13', '73000000-0000-1000-8000-000000000004', '2026-10-13 00:00:00', '2026-10-26 23:59:59', 'Company-beta-labs'],
        ];

        foreach ($sprints as [$reference, $uuid, $startDate, $endDate, $companyReference]) {
            $company = $this->getReference($companyReference, Company::class);

            $sprint = (new Sprint())
                ->setStartDate(new DateTimeImmutable($startDate))
                ->setEndDate(new DateTimeImmutable($endDate))
                ->setCompany($company);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $sprint);
            $manager->persist($sprint);
            $this->addReference($reference, $sprint);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 11;
    }
}
