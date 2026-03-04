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
        foreach ($this->getApplications() as $keyName => $data) {
            $application = (new Application())
                ->setKeyName($keyName)
                ->setName($data['name'])
                ->setLogo($data['logo'])
                ->setDescription($data['description'])
                ->setActive(true);

            $manager->persist($application);
            $this->addReference('Application-' . $data['name'], $application);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 1;
    }

    /**
     * @return array<string, array{name: string, logo: string, description: string}>
     */
    private function getApplications(): array
    {
        return [
            'crm' => [
                'name' => 'CRM',
                'logo' => 'https://ui-avatars.com/api/?name=CRM&background=0D8ABC&color=fff&size=256',
                'description' => "Module CRM complet pour centraliser les relations clients, suivre les opportunités commerciales et piloter les interactions du support sur un historique unifié et exploitable.",
            ],
            'recruit' => [
                'name' => 'Recruit',
                'logo' => 'https://ui-avatars.com/api/?name=Recruit&background=4F46E5&color=fff&size=256',
                'description' => "Espace recrutement pour publier des offres, qualifier les candidatures et orchestrer le cycle d'embauche avec des étapes collaboratives entre RH et managers.",
            ],
            'shop' => [
                'name' => 'Shop',
                'logo' => 'https://ui-avatars.com/api/?name=Shop&background=059669&color=fff&size=256',
                'description' => "Suite e-commerce pour gérer le catalogue produits, les commandes, la facturation et le suivi logistique avec une vue opérationnelle de bout en bout.",
            ],
            'school' => [
                'name' => 'School',
                'logo' => 'https://ui-avatars.com/api/?name=School&background=DC2626&color=fff&size=256',
                'description' => "Module scolaire pour organiser les classes, planifier les enseignements, diffuser les contenus pédagogiques et suivre la progression des apprenants.",
            ],
        ];
    }
}
