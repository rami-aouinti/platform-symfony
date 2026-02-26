<?php

declare(strict_types=1);

namespace App\Recruit\Infrastructure\DataFixtures\ORM;

use App\Company\Domain\Entity\Company;
use App\General\Domain\Rest\UuidHelper;
use App\Recruit\Domain\Entity\Offer;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Offer
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */

final class LoadOfferData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var Company $acme */
        $acme = $this->getReference('Company-acme-demo', Company::class);
        /** @var Company $external */
        $external = $this->getReference('Company-external-corp', Company::class);
        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);

        $offers = [
            ['Offer-php-backend-engineer', '40000000-0000-1000-8000-000000000001', 'PHP Backend Engineer', 'Build secure APIs with Symfony.', 'published', $acme, $john],
            ['Offer-sre-platform', '40000000-0000-1000-8000-000000000002', 'SRE Platform Engineer', 'Drive reliability and observability.', 'published', $acme, $alice],
            ['Offer-data-analyst-external', '40000000-0000-1000-8000-000000000003', 'Data Analyst', 'Create decision-ready dashboards.', 'draft', $external, $carol],
            ['Offer-legacy-support', '40000000-0000-1000-8000-000000000004', 'Legacy Support Specialist', 'Maintain old modules and migrations.', 'archived', $acme, $john],
        ];

        foreach ($offers as [$reference, $uuid, $title, $description, $status, $company, $author]) {
            $offer = (new Offer())
                ->setTitle($title)
                ->setDescription($description)
                ->setStatus($status)
                ->setCompany($company)
                ->setCreatedBy($author);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $offer);
            $manager->persist($offer);
            $this->addReference($reference, $offer);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 6;
    }
}
