<?php

declare(strict_types=1);

namespace App\PluginCatalog\Infrastructure\DataFixtures\ORM;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Domain\Entity\Plugin;
use App\PluginCatalog\Domain\Entity\UserApplicationPlugin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadUserApplicationPluginData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $links = [
            ['application' => 'CRM', 'plugin' => 'notification', 'active' => true],
            ['application' => 'CRM', 'plugin' => 'chat', 'active' => true],
            ['application' => 'CRM', 'plugin' => 'blog', 'active' => false],

            ['application' => 'Shop', 'plugin' => 'notification', 'active' => true],
            ['application' => 'Shop', 'plugin' => 'chat', 'active' => false],

            ['application' => 'Recruit', 'plugin' => 'notification', 'active' => true],
            ['application' => 'Recruit', 'plugin' => 'blog', 'active' => true],

            ['application' => 'School', 'plugin' => 'chat', 'active' => false],
            ['application' => 'School', 'plugin' => 'blog', 'active' => false],
        ];

        foreach ($links as $link) {
            $userApplication = $this->getReference('UserApplication-john-root-' . $link['application'], UserApplication::class);
            $plugin = $this->getReference('Plugin-' . $link['plugin'], Plugin::class);

            $manager->persist(
                (new UserApplicationPlugin($userApplication, $plugin))
                    ->setActive($link['active']),
            );
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 5;
    }
}
