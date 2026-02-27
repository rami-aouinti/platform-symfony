<?php

declare(strict_types=1);

namespace App\Company\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\Company\Domain\Entity\CompanyMembership;
use App\Company\Domain\Enum\CompanyMembershipStatus;
use App\Company\Domain\Enum\CompanyStatus;
use App\General\Domain\Entity\Address as AddressValueObject;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Company\Infrastructure\DataFixtures\ORM
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

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
        /** @var User $candidateUser */
        $candidateUser = $this->getReference('User-hugo-user', User::class);

        $acme = (new Company())
            ->setLegalName('Acme Demo')
            ->setSlug('acme-demo')
            ->setStatus(CompanyStatus::ACTIVE)
            ->setMainAddress((new AddressValueObject())->setStreetLine1('1 Demo Street')->setPostalCode('75001')->setCity('Paris')->setRegion('Île-de-France')->setCountryCode('FR'))
            ->setOwner($owner);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000001'), $acme);

        $acmeOwnerMembership = (new CompanyMembership($owner, $acme))
            ->setRole(CompanyMembership::ROLE_OWNER)
            ->setStatus(CompanyMembershipStatus::ACTIVE)
            ->setJoinedAt(new DateTimeImmutable('2026-01-05 09:00:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000002'), $acmeOwnerMembership);

        $acmeManagerMembership = (new CompanyMembership($managerUser, $acme))
            ->setRole(CompanyMembership::ROLE_CRM_MANAGER)
            ->setStatus(CompanyMembershipStatus::ACTIVE)
            ->setJoinedAt(new DateTimeImmutable('2026-01-12 10:30:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000004'), $acmeManagerMembership);

        $acmeCandidateMembership = (new CompanyMembership($candidateUser, $acme))
            ->setRole(CompanyMembership::ROLE_CANDIDATE)
            ->setStatus(CompanyMembershipStatus::INVITED)
            ->setInvitedAt(new DateTimeImmutable('2026-02-01 14:00:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000007'), $acmeCandidateMembership);

        $externalCompany = (new Company())
            ->setLegalName('External Corp')
            ->setSlug('external-corp')
            ->setStatus(CompanyStatus::ACTIVE)
            ->setMainAddress((new AddressValueObject())->setStreetLine1('2 External Street')->setPostalCode('69000')->setCity('Lyon')->setRegion('Auvergne-Rhône-Alpes')->setCountryCode('FR'))
            ->setOwner($externalUser);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000005'), $externalCompany);

        $externalOwnerMembership = (new CompanyMembership($externalUser, $externalCompany))
            ->setRole(CompanyMembership::ROLE_OWNER)
            ->setStatus(CompanyMembershipStatus::ACTIVE)
            ->setJoinedAt(new DateTimeImmutable('2026-01-15 11:00:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000006'), $externalOwnerMembership);

        $betaCompany = (new Company())
            ->setLegalName('Beta Labs')
            ->setSlug('beta-labs')
            ->setStatus(CompanyStatus::SUSPENDED)
            ->setMainAddress((new AddressValueObject())->setStreetLine1('77 Innovation Avenue')->setPostalCode('31000')->setCity('Toulouse')->setRegion('Occitanie')->setCountryCode('FR'))
            ->setOwner($managerUser);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000008'), $betaCompany);

        $betaOwnerMembership = (new CompanyMembership($managerUser, $betaCompany))
            ->setRole(CompanyMembership::ROLE_OWNER)
            ->setStatus(CompanyMembershipStatus::ACTIVE)
            ->setJoinedAt(new DateTimeImmutable('2026-01-20 08:00:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000009'), $betaOwnerMembership);

        $betaTeacherMembership = (new CompanyMembership($owner, $betaCompany))
            ->setRole(CompanyMembership::ROLE_TEACHER)
            ->setStatus(CompanyMembershipStatus::INVITED)
            ->setInvitedAt(new DateTimeImmutable('2026-02-10 16:15:00'));
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000010'), $betaTeacherMembership);

        $manager->persist($acme);
        $manager->persist($acmeOwnerMembership);
        $manager->persist($acmeManagerMembership);
        $manager->persist($acmeCandidateMembership);

        $manager->persist($externalCompany);
        $manager->persist($externalOwnerMembership);

        $manager->persist($betaCompany);
        $manager->persist($betaOwnerMembership);
        $manager->persist($betaTeacherMembership);
        $manager->flush();

        $this->addReference('Company-acme-demo', $acme);
        $this->addReference('Company-external-corp', $externalCompany);
        $this->addReference('Company-beta-labs', $betaCompany);

        $this->addReference('CompanyMembership-acme-owner-john', $acmeOwnerMembership);
        $this->addReference('CompanyMembership-acme-manager-alice', $acmeManagerMembership);
        $this->addReference('CompanyMembership-acme-candidate-hugo', $acmeCandidateMembership);
        $this->addReference('CompanyMembership-external-owner-carol', $externalOwnerMembership);
        $this->addReference('CompanyMembership-beta-owner-alice', $betaOwnerMembership);
        $this->addReference('CompanyMembership-beta-teacher-john', $betaTeacherMembership);
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
