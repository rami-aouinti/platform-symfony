<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\Recruit\Domain\Entity\CandidateProfile;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Candidate
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class LoadCandidateProfileData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('User-john', User::class);
        /** @var Company $company */
        $company = $this->getReference('Company-acme-demo', Company::class);

        $candidateProfile = (new CandidateProfile($user))
            ->setCompany($company)
            ->setStatus('new');

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('30000000-0000-1000-8000-000000000003'), $candidateProfile);

        $manager->persist($candidateProfile);
        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 5;
    }
}
