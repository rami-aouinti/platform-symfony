<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyMembership;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadCompanyData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $owner */
        $owner = $this->getReference('User-john-user', User::class);
        /** @var User $managerUser */
        $managerUser = $this->getReference('User-alice-user', User::class);
        /** @var User $externalUser */
        $externalUser = $this->getReference('User-carol-user', User::class);

        $company = (new Company())
            ->setLegalName('Acme Demo')
            ->setSlug('acme-demo')
            ->setStatus('active')
            ->setMainAddress('1 Demo Street, 75001 Paris')
            ->setOwner($owner);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000001'), $company);

        $ownerMembership = (new CompanyMembership($owner, $company))
            ->setRole(CompanyMembership::ROLE_OWNER)
            ->setStatus('active');

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000002'), $ownerMembership);

        $managerMembership = (new CompanyMembership($managerUser, $company))
            ->setRole(CompanyMembership::ROLE_CRM_MANAGER)
            ->setStatus('active');

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000004'), $managerMembership);

        $externalCompany = (new Company())
            ->setLegalName('External Corp')
            ->setSlug('external-corp')
            ->setStatus('active')
            ->setMainAddress('2 External Street, 69000 Lyon')
            ->setOwner($externalUser);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000005'), $externalCompany);

        $externalMembership = (new CompanyMembership($externalUser, $externalCompany))
            ->setRole(CompanyMembership::ROLE_OWNER)
            ->setStatus('active');

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000006'), $externalMembership);

        $manager->persist($company);
        $manager->persist($ownerMembership);
        $manager->persist($managerMembership);
        $manager->persist($externalCompany);
        $manager->persist($externalMembership);
        $manager->flush();

        $this->addReference('Company-acme-demo', $company);
        $this->addReference('Company-external-corp', $externalCompany);
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
