<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Infrastructure\DataFixtures\ORM;

use App\ApplicationCatalog\Domain\Entity\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadApplicationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getApplications() as $name => $logo) {
            $application = (new Application())
                ->setName($name)
                ->setLogo($logo)
                ->setActive(true);

            $manager->persist($application);
            $this->addReference('Application-' . $name, $application);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 1;
    }

    /**
     * @return array<string, string|null>
     */
    private function getApplications(): array
    {
        return [
            'CRM' => null,
            'Shop' => null,
            'Recruit' => null,
            'School' => null,
        ];
    }
}
