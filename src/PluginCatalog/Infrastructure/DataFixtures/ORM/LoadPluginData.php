<?php

declare(strict_types=1);

namespace App\PluginCatalog\Infrastructure\DataFixtures\ORM;

use App\PluginCatalog\Domain\Entity\Plugin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadPluginData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getPlugins() as $keyName => $data) {
            $plugin = (new Plugin())
                ->setKeyName($keyName)
                ->setName($data['name'])
                ->setLogo($data['logo'])
                ->setDescription($data['description'])
                ->setActive(true);

            $manager->persist($plugin);
            $this->addReference('Plugin-' . $keyName, $plugin);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 2;
    }

    /**
     * @return array<string, array{name: string, logo: string, description: string}>
     */
    private function getPlugins(): array
    {
        return [
            'notification' => [
                'name' => 'Notification',
                'logo' => 'https://cdn.fake.example/plugins/notification-logo.png',
                'description' => 'Envoi des notifications e-mail, push et in-app.',
            ],
            'chat' => [
                'name' => 'Chat',
                'logo' => 'https://cdn.fake.example/plugins/chat-logo.png',
                'description' => 'Messagerie instantanée entre utilisateurs et équipes.',
            ],
            'blog' => [
                'name' => 'Blog',
                'logo' => 'https://cdn.fake.example/plugins/blog-logo.png',
                'description' => 'Publication d’articles et gestion éditoriale.',
            ],
        ];
    }
}
