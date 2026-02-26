<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Enum\ProjectStatus;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadProjectData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $john */
        $john = $this->getReference('User-john-user', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);

        $projects = [
            ['Project-platform-rebuild', '70000000-0000-1000-8000-000000000001', 'Platform Rebuild', 'Migration technique progressive du legacy.', ProjectStatus::ACTIVE, $john],
            ['Project-hiring-automation', '70000000-0000-1000-8000-000000000002', 'Hiring Automation', 'Automatisation du workflow de recrutement.', ProjectStatus::ACTIVE, $alice],
            ['Project-design-system', '70000000-0000-1000-8000-000000000003', 'Design System', 'Composants UI partagés pour web et mobile.', ProjectStatus::ACTIVE, $alice],
            ['Project-data-cleanup', '70000000-0000-1000-8000-000000000004', 'Data Cleanup', 'Nettoyage et consolidation des données historiques.', ProjectStatus::ARCHIVED, $carol],
            ['Project-b2b-portal', '70000000-0000-1000-8000-000000000005', 'B2B Portal', 'Portail client entreprise en self-service.', ProjectStatus::ACTIVE, $carol],
            ['Project-observability', '70000000-0000-1000-8000-000000000006', 'Observability', 'Logs, métriques et alerting centralisés.', ProjectStatus::ACTIVE, $john],
            ['Project-security-hardening', '70000000-0000-1000-8000-000000000007', 'Security Hardening', 'Renforcement sécurité API et IAM.', ProjectStatus::ACTIVE, $john],
            ['Project-internal-tools', '70000000-0000-1000-8000-000000000008', 'Internal Tools', 'Outils internes pour support et ops.', ProjectStatus::ARCHIVED, $alice],
        ];

        foreach ($projects as [$reference, $uuid, $name, $description, $status, $owner]) {
            $project = (new Project())
                ->setName($name)
                ->setDescription($description)
                ->setStatus($status)
                ->setOwner($owner);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $project);
            $manager->persist($project);
            $this->addReference($reference, $project);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 9;
    }
}
