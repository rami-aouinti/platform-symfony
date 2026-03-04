<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Page\Domain\Entity\About;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadPageAboutData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['PageAbout-company', '7a000000-0000-1000-8000-000000000001', 'Company', 'Présentation de l’entreprise, de sa mission et de ses valeurs.'],
            ['PageAbout-platform', '7a000000-0000-1000-8000-000000000002', 'Platform', 'Détails sur la plateforme, ses fonctionnalités principales et les bénéfices pour les utilisateurs.'],
            ['PageAbout-team', '7a000000-0000-1000-8000-000000000003', 'Team', 'Description de l’équipe, de son expertise et de son engagement envers la qualité.'],
        ];

        foreach ($rows as [$reference, $uuid, $name, $description]) {
            $about = (new About())
                ->setName($name)
                ->setDescription($description);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $about);
            $manager->persist($about);
            $this->addReference($reference, $about);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 13;
    }
}
