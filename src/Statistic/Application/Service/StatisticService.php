<?php

declare(strict_types=1);

namespace App\Statistic\Application\Service;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

use function array_filter;
use function array_map;
use function count;
use function sprintf;
use function str_contains;

class StatisticService
{
    private const DATE_FIELDS = [
        'createdAt',
        'created_at',
    ];

    /**
     * @var array<string, array{class: class-string, field: string}>
     */
    private const STATUS_DISTRIBUTION_TARGETS = [
        'tasks' => ['class' => 'App\Task\Domain\Entity\Task', 'field' => 'status'],
        'jobApplications' => ['class' => 'App\Recruit\Domain\Entity\JobApplication', 'field' => 'status'],
        'jobOffers' => ['class' => 'App\Recruit\Domain\Entity\JobOffer', 'field' => 'status'],
        'offers' => ['class' => 'App\Recruit\Domain\Entity\Offer', 'field' => 'status'],
    ];

    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function getOverview(): array
    {
        $entities = $this->getEntityCountMap();
        $cards = [
            [
                'icon' => 'mdi-account-group',
                'title' => 'Users',
                'value' => $entities['User'] ?? 0,
                'color' => 'primary',
                'caption' => 'Nombre total des utilisateurs',
            ],
            [
                'icon' => 'mdi-office-building',
                'title' => 'Companies',
                'value' => $entities['Company'] ?? 0,
                'color' => 'primary',
                'caption' => 'Entreprises actives sur la plateforme',
            ],
            [
                'icon' => 'mdi-briefcase',
                'title' => 'Job Offers',
                'value' => $entities['JobOffer'] ?? 0,
                'color' => 'warning',
                'caption' => 'Offres publiées',
            ],
            [
                'icon' => 'mdi-clipboard-list',
                'title' => 'Applications',
                'value' => $entities['JobApplication'] ?? 0,
                'color' => 'success',
                'caption' => 'Candidatures soumises',
            ],
            [
                'icon' => 'mdi-format-list-checks',
                'title' => 'Tasks',
                'value' => $entities['Task'] ?? 0,
                'color' => 'info',
                'caption' => 'Tâches créées',
            ],
            [
                'icon' => 'mdi-file-document-multiple',
                'title' => 'Resumes',
                'value' => $entities['Resume'] ?? 0,
                'color' => 'error',
                'caption' => 'CV enregistrés',
            ],
        ];

        return [
            'cards' => $cards,
            'totalEntities' => count($entities),
            'totalRecords' => array_sum($entities),
        ];
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    public function getEntityCounters(): array
    {
        $entityManager = $this->getEntityManager();
        $metadataList = $entityManager->getMetadataFactory()->getAllMetadata();
        $rows = [];

        foreach ($metadataList as $metadata) {
            if (!$metadata instanceof ClassMetadata || !$metadata->isRootEntity()) {
                continue;
            }

            $count = (int) $entityManager->createQueryBuilder()
                ->select('COUNT(entityAlias)')
                ->from($metadata->getName(), 'entityAlias')
                ->getQuery()
                ->getSingleScalarResult();

            $rows[] = [
                'entity' => $metadata->getReflectionClass()->getShortName(),
                'class' => $metadata->getName(),
                'table' => $metadata->getTableName(),
                'count' => $count,
            ];
        }

        usort($rows, static fn (array $left, array $right): int => $right['count'] <=> $left['count']);

        return $rows;
    }

    /**
     * @return array<string, mixed>
     */
    public function getTimeSeries(int $days): array
    {
        $days = max(1, min($days, 365));
        $dateFrom = (new DateTimeImmutable('today'))->modify(sprintf('-%d days', $days - 1));
        $points = [];
        $period = new DatePeriod($dateFrom, new DateInterval('P1D'), $days);

        foreach ($period as $date) {
            $points[$date->format('Y-m-d')] = 0;
        }

        $entityManager = $this->getEntityManager();
        $metadataList = array_filter(
            $entityManager->getMetadataFactory()->getAllMetadata(),
            fn (ClassMetadata $metadata): bool => $metadata->isRootEntity() && $this->hasCreatedField($metadata),
        );

        foreach ($metadataList as $metadata) {
            $alias = 'entityAlias';
            $dateField = $this->resolveCreatedField($metadata);
            if ($dateField === null) {
                continue;
            }

            $rows = $entityManager->createQueryBuilder()
                ->select(sprintf('DATE(%s.%s) as day', $alias, $dateField))
                ->addSelect(sprintf('COUNT(%s) as total', $alias))
                ->from($metadata->getName(), $alias)
                ->where(sprintf('%s.%s >= :dateFrom', $alias, $dateField))
                ->setParameter('dateFrom', $dateFrom)
                ->groupBy('day')
                ->getQuery()
                ->getArrayResult();

            foreach ($rows as $row) {
                $day = (string) ($row['day'] ?? '');

                if (!str_contains($day, '-')) {
                    continue;
                }

                $normalizedDay = (new DateTimeImmutable($day))->format('Y-m-d');
                $points[$normalizedDay] = ($points[$normalizedDay] ?? 0) + (int) ($row['total'] ?? 0);
            }
        }

        return [
            'from' => $dateFrom->format(DateTimeImmutable::ATOM),
            'to' => (new DateTimeImmutable('today'))->format(DateTimeImmutable::ATOM),
            'days' => $days,
            'entity' => 'all',
            'series' => array_map(
                static fn (string $day, int $total): array => ['date' => $day, 'value' => $total],
                array_keys($points),
                array_values($points),
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getTimeSeriesByEntity(string $entity, int $days): array
    {
        $days = max(1, min($days, 365));
        $dateFrom = (new DateTimeImmutable('today'))->modify(sprintf('-%d days', $days - 1));
        $metadata = $this->findMetadataByEntity($entity);

        if ($metadata === null) {
            throw new RuntimeException(sprintf('Unknown entity "%s".', $entity));
        }

        $dateField = $this->resolveCreatedField($metadata);

        if ($dateField === null) {
            throw new RuntimeException(sprintf('Entity "%s" does not have a created date field.', $entity));
        }

        $points = [];
        $period = new DatePeriod($dateFrom, new DateInterval('P1D'), $days);

        foreach ($period as $date) {
            $points[$date->format('Y-m-d')] = 0;
        }

        $entityManager = $this->getEntityManager();
        $rows = $entityManager->createQueryBuilder()
            ->select(sprintf('DATE(entityAlias.%s) as day', $dateField))
            ->addSelect('COUNT(entityAlias) as total')
            ->from($metadata->getName(), 'entityAlias')
            ->where(sprintf('entityAlias.%s >= :dateFrom', $dateField))
            ->setParameter('dateFrom', $dateFrom)
            ->groupBy('day')
            ->getQuery()
            ->getArrayResult();

        foreach ($rows as $row) {
            $day = (string) ($row['day'] ?? '');

            if (!str_contains($day, '-')) {
                continue;
            }

            $normalizedDay = (new DateTimeImmutable($day))->format('Y-m-d');
            $points[$normalizedDay] = (int) ($row['total'] ?? 0);
        }

        return [
            'from' => $dateFrom->format(DateTimeImmutable::ATOM),
            'to' => (new DateTimeImmutable('today'))->format(DateTimeImmutable::ATOM),
            'days' => $days,
            'entity' => $metadata->getReflectionClass()->getShortName(),
            'series' => array_map(
                static fn (string $day, int $total): array => ['date' => $day, 'value' => $total],
                array_keys($points),
                array_values($points),
            ),
        ];
    }

    /**
     * @return array<string, array<int, array{label: string, value: int}>>
     */
    public function getStatusDistributions(): array
    {
        $entityManager = $this->getEntityManager();
        $payload = [];

        foreach (self::STATUS_DISTRIBUTION_TARGETS as $key => $target) {
            if (!class_exists($target['class'])) {
                continue;
            }

            $rows = $entityManager->createQueryBuilder()
                ->select(sprintf('entityAlias.%s as label', $target['field']))
                ->addSelect('COUNT(entityAlias) as value')
                ->from($target['class'], 'entityAlias')
                ->groupBy('label')
                ->getQuery()
                ->getArrayResult();

            $payload[$key] = array_map(
                static fn (array $row): array => [
                    'label' => (string) ($row['label'] ?? ''),
                    'value' => (int) ($row['value'] ?? 0),
                ],
                $rows,
            );
        }

        return $payload;
    }

    /**
     * @return array<string, int>
     */
    public function getEntityCountMap(): array
    {
        $rows = $this->getEntityCounters();
        $map = [];

        foreach ($rows as $row) {
            $map[(string) $row['entity']] = (int) $row['count'];
        }

        return $map;
    }

    private function getEntityManager(): EntityManagerInterface
    {
        $entityManager = $this->managerRegistry->getManager();

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Default manager is not an EntityManagerInterface.');
        }

        return $entityManager;
    }

    private function findMetadataByEntity(string $entity): ?ClassMetadata
    {
        $entityManager = $this->getEntityManager();
        $normalized = strtolower($entity);

        foreach ($entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            if (!$metadata instanceof ClassMetadata || !$metadata->isRootEntity()) {
                continue;
            }

            if (strtolower($metadata->getName()) === $normalized
                || strtolower($metadata->getReflectionClass()->getShortName()) === $normalized
            ) {
                return $metadata;
            }
        }

        return null;
    }

    private function hasCreatedField(ClassMetadata $metadata): bool
    {
        foreach (self::DATE_FIELDS as $field) {
            if ($metadata->hasField($field)) {
                return true;
            }
        }

        return false;
    }

    private function resolveCreatedField(ClassMetadata $metadata): ?string
    {
        foreach (self::DATE_FIELDS as $field) {
            if ($metadata->hasField($field)) {
                return $field;
            }
        }

        return null;
    }
}

