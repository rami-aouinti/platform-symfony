<?php

declare(strict_types=1);

namespace App\JobApplication\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\JobApplication\Domain\Entity\JobApplication;
use App\JobApplication\Domain\Enum\ApplicationStatus;
use App\Offer\Domain\Entity\Offer;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadJobApplicationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var Offer $offer */
        $offer = $this->getReference('Offer-php-backend-engineer', Offer::class);
        /** @var User $candidate */
        $candidate = $this->getReference('User-carol-user', User::class);

        $application = (new JobApplication($offer, $candidate))
            ->setStatus(ApplicationStatus::PENDING);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('50000000-0000-1000-8000-000000000001'), $application);

        $manager->persist($application);
        $manager->flush();

        $this->addReference('JobApplication-carol-on-php-backend-engineer', $application);
    }

    #[Override]
    public function getOrder(): int
    {
        return 7;
    }
}
