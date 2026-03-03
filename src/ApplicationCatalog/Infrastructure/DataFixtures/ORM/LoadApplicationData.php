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
        foreach ($this->getApplications() as $name => $data) {
            $application = (new Application())
                ->setName($name)
                ->setLogo($data['logo'])
                ->setDescription($data['description'])
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
     * @return array<string, array{logo: string, description: string}>
     */
    private function getApplications(): array
    {
        return [
            'CRM' => [
                'logo' => 'https://cdn.fake.example/apps/crm-logo.png',
                'description' => 'Centralise les relations clients, le suivi commercial et le support.',
            ],
            'Shop' => [
                'logo' => 'https://cdn.fake.example/apps/shop-logo.png',
                'description' => 'Gère les produits, commandes, stocks et paiements e-commerce.',
            ],
            'Recruit' => [
                'logo' => 'https://cdn.fake.example/apps/recruit-logo.png',
                'description' => 'Pilote le recrutement : offres, candidatures et workflow RH.',
            ],
            'School' => [
                'logo' => 'https://cdn.fake.example/apps/school-logo.png',
                'description' => 'Organise les cours, élèves, évaluations et communication pédagogique.',
            ],
        ];
    }
}
