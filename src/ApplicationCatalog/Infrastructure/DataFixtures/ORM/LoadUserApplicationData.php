<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Infrastructure\DataFixtures\ORM;

use App\ApplicationCatalog\Domain\Entity\Application;
use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadUserApplicationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $johnRoot = $this->getReference('User-john-root', User::class);

        $activations = [
            ['application' => 'CRM', 'name' => 'CRM 1', 'active' => true],
            ['application' => 'CRM', 'name' => 'CRM 2', 'active' => true],
            ['application' => 'Shop', 'name' => 'Shop Principal', 'active' => true],
            ['application' => 'Recruit', 'name' => 'Recruit Principal', 'active' => true],
            ['application' => 'School', 'name' => 'School Principal', 'active' => false],
        ];

        foreach ($activations as $index => $activation) {
            $application = $this->getReference('Application-' . $activation['application'], Application::class);

            $userApplication = (new UserApplication($johnRoot, $application))
                ->setName($activation['name'])
                ->setActive($activation['active']);

            $manager->persist($userApplication);
            $this->addReference('UserApplication-john-root-' . $index, $userApplication);

            if (!$this->hasReference('UserApplication-john-root-' . $activation['application'], UserApplication::class)) {
                $this->addReference('UserApplication-john-root-' . $activation['application'], $userApplication);
            }
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
