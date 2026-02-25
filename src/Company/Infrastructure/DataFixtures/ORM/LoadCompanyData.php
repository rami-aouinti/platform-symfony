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
        /** @var User $user */
        $user = $this->getReference('User-john-user', User::class);

        $company = (new Company())
            ->setLegalName('Acme Demo')
            ->setSlug('acme-demo')
            ->setStatus('active')
            ->setMainAddress('1 Demo Street, 75001 Paris')
            ->setOwner($user);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000001'), $company);

        $membership = (new CompanyMembership($user, $company))
            ->setRole(CompanyMembership::ROLE_OWNER)
            ->setStatus('active');

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000002'), $membership);

        $manager->persist($company);
        $manager->persist($membership);
        $manager->flush();

        $this->addReference('Company-acme-demo', $company);
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
