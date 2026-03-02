<?php

declare(strict_types=1);

namespace App\Notification\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Notification\Domain\Entity\Notification;
use App\Notification\Domain\Enum\NotificationType;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Notification\Infrastructure\DataFixtures\ORM
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
final class LoadNotificationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $johnRoot */
        $johnRoot = $this->getReference('User-john-root', User::class);

        $notifications = [
            'Notification-john-root-company-created-unread' => (new Notification($johnRoot))
                ->setTitle('New company profile created')
                ->setMessage('Your company profile "Acme Industries" has been created successfully.')
                ->setType(NotificationType::COMPANY_CREATED->value),
            'Notification-john-root-application-submitted-read' => (new Notification($johnRoot))
                ->setTitle('New application submitted')
                ->setMessage('A candidate submitted an application for PHP Backend Engineer.')
                ->setType(NotificationType::JOB_APPLICATION_SUBMITTED->value)
                ->setReadAt(new DateTimeImmutable('2026-02-22 08:15:00')),
            'Notification-john-root-application-decided-unread' => (new Notification($johnRoot))
                ->setTitle('Application decision published')
                ->setMessage('A decision has been published for Platform SRE application.')
                ->setType(NotificationType::JOB_APPLICATION_DECIDED->value),
        ];

        $uuids = [
            'Notification-john-root-company-created-unread' => '70000000-0000-1000-8000-000000000001',
            'Notification-john-root-application-submitted-read' => '70000000-0000-1000-8000-000000000002',
            'Notification-john-root-application-decided-unread' => '70000000-0000-1000-8000-000000000003',
        ];

        foreach ($notifications as $reference => $notification) {
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuids[$reference]), $notification);
            $manager->persist($notification);
        }

        $manager->flush();

        foreach ($notifications as $reference => $notification) {
            $this->addReference($reference, $notification);
        }
    }

    #[Override]
    public function getOrder(): int
    {
        return 9;
    }
}
