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
            'chat' => [
                'name' => 'Chat',
                'logo' => 'https://ui-avatars.com/api/?name=Chat&background=0EA5E9&color=fff&size=256',
                'description' => "Messagerie instantanée pour fluidifier les échanges internes, centraliser les conversations par contexte métier et conserver un historique exploitable.",
            ],
            'blog' => [
                'name' => 'Blog',
                'logo' => 'https://ui-avatars.com/api/?name=Blog&background=7C3AED&color=fff&size=256',
                'description' => "Publication éditoriale pour rédiger des articles, structurer les catégories et renforcer la communication de contenu auprès des utilisateurs.",
            ],
            'notification' => [
                'name' => 'Notification',
                'logo' => 'https://ui-avatars.com/api/?name=Notification&background=2563EB&color=fff&size=256',
                'description' => "Canal de notifications multi-support pour déclencher des alertes ciblées par e-mail, push ou in-app selon les événements métier.",
            ],
            'contact' => [
                'name' => 'Contact',
                'logo' => 'https://ui-avatars.com/api/?name=Contact&background=16A34A&color=fff&size=256',
                'description' => "Formulaires de contact pour capter les demandes entrantes, qualifier les messages et faciliter leur routage vers les bonnes équipes.",
            ],
            'quiz' => [
                'name' => 'Quiz',
                'logo' => 'https://ui-avatars.com/api/?name=Quiz&background=EA580C&color=fff&size=256',
                'description' => "Outil d'évaluation pour créer des questionnaires dynamiques, mesurer les connaissances et analyser les résultats par profil utilisateur.",
            ],
            'log' => [
                'name' => 'Log',
                'logo' => 'https://ui-avatars.com/api/?name=Log&background=475569&color=fff&size=256',
                'description' => "Journalisation transverse pour tracer les actions critiques, investiguer les incidents et renforcer l'auditabilité de la plateforme.",
            ],
            'about' => [
                'name' => 'About',
                'logo' => 'https://ui-avatars.com/api/?name=About&background=DB2777&color=fff&size=256',
                'description' => "Page institutionnelle pour présenter la mission, les engagements et les informations de référence du produit ou de l'organisation.",
            ],
            'calendar' => [
                'name' => 'Calendar',
                'logo' => 'https://ui-avatars.com/api/?name=Calendar&background=0F766E&color=fff&size=256',
                'description' => "Calendrier collaboratif pour planifier les événements, coordonner les disponibilités et synchroniser les échéances importantes.",
            ],
        ];
    }
}
