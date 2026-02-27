<?php

declare(strict_types=1);

namespace App\Task\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Task\Domain\Entity\Project;
use App\Task\Domain\Entity\Task;
use App\Task\Domain\Enum\TaskPriority;
use App\Task\Domain\Enum\TaskStatus;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * LoadTaskData.
 *
 * @package App\Task\Infrastructure\DataFixtures\ORM
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class LoadTaskData extends Fixture implements OrderedFixtureInterface
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

        /** @var Project $platform */
        $platform = $this->getReference('Project-platform-rebuild', Project::class);
        /** @var Project $hiring */
        $hiring = $this->getReference('Project-hiring-automation', Project::class);
        /** @var Project $designSystem */
        $designSystem = $this->getReference('Project-design-system', Project::class);
        /** @var Project $b2bPortal */
        $b2bPortal = $this->getReference('Project-b2b-portal', Project::class);
        /** @var Project $observability */
        $observability = $this->getReference('Project-observability', Project::class);

        $rows = [
            ['Task-platform-auth-refactor', '71000000-0000-1000-8000-000000000001', 'Refactor auth guards', 'Aligner les guards avec policy centralisée.', TaskPriority::HIGH, TaskStatus::IN_PROGRESS, $john, $platform, '2026-09-10 18:00:00'],
            ['Task-platform-cache-tags', '71000000-0000-1000-8000-000000000002', 'Cache tags support', 'Ajout invalidation par tags.', TaskPriority::MEDIUM, TaskStatus::TODO, $john, $platform, '2026-09-12 18:00:00'],
            ['Task-platform-tenant-fix', '71000000-0000-1000-8000-000000000003', 'Fix tenant header parsing', 'Corriger extraction tenant dans edge cases.', TaskPriority::CRITICAL, TaskStatus::DONE, $john, $platform, '2026-09-05 18:00:00'],
            ['Task-platform-rate-limit', '71000000-0000-1000-8000-000000000004', 'Rate limit login', 'Limiter bruteforce sur /login.', TaskPriority::HIGH, TaskStatus::ARCHIVED, $john, $platform, '2026-08-31 18:00:00'],
            ['Task-hiring-candidate-search', '71000000-0000-1000-8000-000000000005', 'Candidate search filters', 'Filtres combinés par compétences.', TaskPriority::HIGH, TaskStatus::IN_PROGRESS, $alice, $hiring, '2026-09-16 18:00:00'],
            ['Task-hiring-application-timeline', '71000000-0000-1000-8000-000000000006', 'Application timeline', 'Afficher historique des transitions.', TaskPriority::MEDIUM, TaskStatus::TODO, $alice, $hiring, '2026-09-20 18:00:00'],
            ['Task-hiring-email-template', '71000000-0000-1000-8000-000000000007', 'Email templates review', 'Uniformiser templates candidats.', TaskPriority::LOW, TaskStatus::DONE, $alice, $hiring, '2026-09-08 18:00:00'],
            ['Task-hiring-export-csv', '71000000-0000-1000-8000-000000000008', 'CSV export pipeline', 'Export sécurisé et paginé.', TaskPriority::MEDIUM, TaskStatus::ARCHIVED, $alice, $hiring, '2026-09-02 18:00:00'],
            ['Task-design-datatable', '71000000-0000-1000-8000-000000000009', 'Data table component', 'Composant table tri et filtres.', TaskPriority::MEDIUM, TaskStatus::TODO, $alice, $designSystem, '2026-09-18 18:00:00'],
            ['Task-design-theme-tokens', '71000000-0000-1000-8000-000000000010', 'Theme tokens', 'Tokens couleurs/espacements globaux.', TaskPriority::HIGH, TaskStatus::IN_PROGRESS, $alice, $designSystem, '2026-09-14 18:00:00'],
            ['Task-design-icon-library', '71000000-0000-1000-8000-000000000011', 'Icon library alignment', 'Normaliser icon set.', TaskPriority::LOW, TaskStatus::DONE, $alice, $designSystem, '2026-09-06 18:00:00'],
            ['Task-design-empty-states', '71000000-0000-1000-8000-000000000012', 'Empty states pack', 'Ajouter états vides communs.', TaskPriority::LOW, TaskStatus::ARCHIVED, $alice, $designSystem, '2026-08-28 18:00:00'],
            ['Task-b2b-contract-upload', '71000000-0000-1000-8000-000000000013', 'Contract upload', 'Upload contrats + scan antivirus.', TaskPriority::CRITICAL, TaskStatus::IN_PROGRESS, $carol, $b2bPortal, '2026-09-11 18:00:00'],
            ['Task-b2b-account-switch', '71000000-0000-1000-8000-000000000014', 'Account switcher', 'Basculer entre sociétés clientes.', TaskPriority::HIGH, TaskStatus::TODO, $carol, $b2bPortal, '2026-09-13 18:00:00'],
            ['Task-b2b-billing-portal', '71000000-0000-1000-8000-000000000015', 'Billing portal sync', 'Synchroniser facturation externe.', TaskPriority::MEDIUM, TaskStatus::DONE, $carol, $b2bPortal, '2026-09-04 18:00:00'],
            ['Task-b2b-mfa-enforcement', '71000000-0000-1000-8000-000000000016', 'MFA enforcement', 'Forcer MFA sur comptes admin client.', TaskPriority::CRITICAL, TaskStatus::ARCHIVED, $carol, $b2bPortal, '2026-08-27 18:00:00'],
            ['Task-observability-tracing', '71000000-0000-1000-8000-000000000017', 'Distributed tracing', 'Tracer appels entre services.', TaskPriority::HIGH, TaskStatus::IN_PROGRESS, $john, $observability, '2026-09-15 18:00:00'],
            ['Task-observability-slo', '71000000-0000-1000-8000-000000000018', 'SLO dashboard', 'KPIs disponibilité et latence.', TaskPriority::MEDIUM, TaskStatus::TODO, $john, $observability, '2026-09-19 18:00:00'],
            ['Task-observability-alert-fatigue', '71000000-0000-1000-8000-000000000019', 'Alert fatigue reduction', 'Regrouper alertes redondantes.', TaskPriority::LOW, TaskStatus::DONE, $john, $observability, '2026-09-03 18:00:00'],
            ['Task-observability-log-sampling', '71000000-0000-1000-8000-000000000020', 'Log sampling policy', 'Réduire coûts stockage logs.', TaskPriority::LOW, TaskStatus::ARCHIVED, $john, $observability, '2026-08-25 18:00:00'],
            ['Task-generic-backlog-1', '71000000-0000-1000-8000-000000000021', 'Backlog grooming #1', 'Tri et priorisation backlog.', TaskPriority::MEDIUM, TaskStatus::TODO, $john, null, '2026-09-22 18:00:00'],
            ['Task-generic-backlog-2', '71000000-0000-1000-8000-000000000022', 'Backlog grooming #2', 'Epics à découper en stories.', TaskPriority::LOW, TaskStatus::TODO, $alice, null, '2026-09-23 18:00:00'],
            ['Task-generic-spike-1', '71000000-0000-1000-8000-000000000023', 'Spike GraphQL federation', 'Étude faisabilité federation.', TaskPriority::HIGH, TaskStatus::IN_PROGRESS, $carol, null, '2026-09-24 18:00:00'],
            ['Task-generic-spike-2', '71000000-0000-1000-8000-000000000024', 'Spike offline sync', 'Étude sync offline mobile.', TaskPriority::MEDIUM, TaskStatus::TODO, $john, null, '2026-09-25 18:00:00'],
            ['Task-generic-doc-1', '71000000-0000-1000-8000-000000000025', 'Architecture docs', 'Mettre à jour ADRs.', TaskPriority::LOW, TaskStatus::DONE, $alice, null, '2026-09-01 18:00:00'],
            ['Task-generic-doc-2', '71000000-0000-1000-8000-000000000026', 'API styleguide', 'Conventions endpoints REST.', TaskPriority::LOW, TaskStatus::ARCHIVED, $carol, null, '2026-08-22 18:00:00'],
            ['Task-generic-security-1', '71000000-0000-1000-8000-000000000027', 'Secret rotation', 'Rotation clés et tokens.', TaskPriority::CRITICAL, TaskStatus::IN_PROGRESS, $john, null, '2026-09-09 18:00:00'],
            ['Task-generic-security-2', '71000000-0000-1000-8000-000000000028', 'Dependency audit', 'Vuln scan dépendances.', TaskPriority::HIGH, TaskStatus::TODO, $alice, null, '2026-09-17 18:00:00'],
            ['Task-generic-ops-1', '71000000-0000-1000-8000-000000000029', 'CI pipeline optimization', 'Paralléliser jobs CI.', TaskPriority::MEDIUM, TaskStatus::DONE, $carol, null, '2026-08-30 18:00:00'],
            ['Task-generic-ops-2', '71000000-0000-1000-8000-000000000030', 'Blue/green deploy script', 'Script rollback automatique.', TaskPriority::HIGH, TaskStatus::ARCHIVED, $john, null, '2026-08-20 18:00:00'],
        ];

        foreach ($rows as [$reference, $uuid, $title, $description, $priority, $status, $owner, $project, $dueDate]) {
            $task = (new Task())
                ->setTitle($title)
                ->setDescription($description)
                ->setPriority($priority)
                ->setStatus($status)
                ->setOwner($owner)
                ->setProject($project)
                ->setDueDate(new DateTimeImmutable($dueDate));

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $task);
            $manager->persist($task);
            $this->addReference($reference, $task);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 10;
    }
}
