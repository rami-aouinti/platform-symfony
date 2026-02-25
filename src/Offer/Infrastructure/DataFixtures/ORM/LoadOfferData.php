<?php

declare(strict_types=1);

namespace App\Offer\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\Offer\Domain\Entity\Offer;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadOfferData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var Company $company */
        $company = $this->getReference('Company-acme-demo', Company::class);
        /** @var User $author */
        $author = $this->getReference('User-john-user', User::class);

        $offer = (new Offer())
            ->setTitle('PHP Backend Engineer')
            ->setDescription('Build secure APIs with Symfony.')
            ->setStatus('published')
            ->setCompany($company)
            ->setCreatedBy($author);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('40000000-0000-1000-8000-000000000001'), $offer);

        $manager->persist($offer);
        $manager->flush();

        $this->addReference('Offer-php-backend-engineer', $offer);
    }

    #[Override]
    public function getOrder(): int
    {
        return 6;
    }
}
