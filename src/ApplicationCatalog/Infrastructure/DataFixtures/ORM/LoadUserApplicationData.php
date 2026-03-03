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
            'CRM' => true,
            'Shop' => true,
            'Recruit' => true,
            'School' => false,
        ];

        foreach ($activations as $applicationName => $active) {
            $application = $this->getReference('Application-' . $applicationName, Application::class);

            $userApplication = (new UserApplication($johnRoot, $application))
                ->setActive($active);

            $this->addReference('UserApplication-john-root-' . mb_strtolower($applicationName), $userApplication);

            $manager->persist($userApplication);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 4;
    }
}
