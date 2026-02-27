<?php

declare(strict_types=1);

namespace App\Calendar\Infrastructure\DataFixtures\ORM;

use App\Calendar\Domain\Entity\Event;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * LoadEventData.
 *
 * @package App\Calendar\Infrastructure\DataFixtures\ORM
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class LoadEventData extends Fixture implements OrderedFixtureInterface
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

        $rows = [
            [
                'Event-team-weekly-sync',
                '72000000-0000-1000-8000-000000000001',
                'Team Weekly Sync',
                'Synchronisation hebdomadaire équipe produit',
                'Salle Neptune',
                $john,
                '2026-07-01 09:00:00',
                '2026-07-01 10:00:00',
                false,
                'Europe/Paris',
                'confirmed',
                'private',
                false,
                '#1d4ed8',
            ],
            [
                'Event-product-demo',
                '72000000-0000-1000-8000-000000000002',
                'Product Demo',
                'Démo mensuelle aux stakeholders',
                'Visio Meet',
                $alice,
                '2026-07-05 14:00:00',
                '2026-07-05 15:30:00',
                false,
                'Europe/Paris',
                'tentative',
                'public',
                false,
                '#059669',
            ],
            [
                'Event-offsite-day',
                '72000000-0000-1000-8000-000000000003',
                'Offsite Day',
                'Journée d\'équipe hors site',
                'Lyon',
                $carol,
                '2026-07-10 00:00:00',
                '2026-07-10 23:59:00',
                true,
                'Europe/Paris',
                'confirmed',
                'private',
                false,
                '#7c3aed',
            ],
            [
                'Event-maintenance-window',
                '72000000-0000-1000-8000-000000000004',
                'Maintenance Window',
                'Maintenance infra planifiée',
                'Datacenter',
                null,
                '2026-07-12 22:00:00',
                '2026-07-13 02:00:00',
                false,
                'UTC',
                'cancelled',
                'public',
                true,
                '#dc2626',
            ],
            [
                'Event-recurring-daily-standup',
                '72000000-0000-1000-8000-000000000005',
                'Daily Standup',
                'Standup quotidien équipe dev',
                'Canal #daily',
                $john,
                '2026-07-01 10:00:00',
                '2026-07-01 10:15:00',
                false,
                'Europe/Paris',
                'confirmed',
                'private',
                false,
                '#0ea5e9',
            ],
        ];

        foreach ($rows as [$reference, $uuid, $title, $description, $location, $user, $startAt, $endAt, $isAllDay, $timezone, $status, $visibility, $isCancelled, $color]) {
            $event = (new Event())
                ->setTitle($title)
                ->setDescription($description)
                ->setLocation($location)
                ->setUser($user)
                ->setStartAt(new DateTimeImmutable($startAt))
                ->setEndAt(new DateTimeImmutable($endAt))
                ->setIsAllDay($isAllDay)
                ->setTimezone($timezone)
                ->setStatus($status)
                ->setVisibility($visibility)
                ->setIsCancelled($isCancelled)
                ->setColor($color)
                ->setBackgroundColor($color)
                ->setBorderColor($color)
                ->setTextColor('#ffffff')
                ->setOrganizerName('Platform Team')
                ->setOrganizerEmail('platform@test.com')
                ->setAttendees([
                    ['name' => 'John', 'email' => 'john.user@test.com'],
                    ['name' => 'Alice', 'email' => 'alice.user@test.com'],
                ])
                ->setReminders([
                    ['type' => 'email', 'minutesBefore' => 30],
                    ['type' => 'popup', 'minutesBefore' => 10],
                ])
                ->setMetadata(['source' => 'fixtures', 'version' => 1]);

            if ($reference === 'Event-recurring-daily-standup') {
                $event
                    ->setRrule('FREQ=DAILY;INTERVAL=1;BYDAY=MO,TU,WE,TH,FR')
                    ->setRecurrenceCount(20)
                    ->setRecurrenceEndAt(new DateTimeImmutable('2026-07-31 10:15:00'))
                    ->setRecurrenceExceptions(['2026-07-14']);
            }

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $event);
            $manager->persist($event);
            $this->addReference($reference, $event);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 11;
    }
}
