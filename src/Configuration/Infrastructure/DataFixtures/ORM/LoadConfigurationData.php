<?php

declare(strict_types=1);

namespace App\Configuration\Infrastructure\DataFixtures\ORM;

use App\Configuration\Domain\Entity\Configuration;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

use function mb_strtoupper;

final class LoadConfigurationData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $johnUser */
        $johnUser = $this->getReference('User-john-root', User::class);
        $johnProfile = $johnUser->getOrCreateUserProfile();

        $notificationConfiguration = (new Configuration())
            ->setCode('profile-notification-settings')
            ->setKeyName('notification')
            ->setStatus('active')
            ->setProfile($johnProfile)
            ->setValue([
                [
                    'key' => 'mentions',
                    'title' => 'Mentions',
                    'description' => 'Notify when another user mentions you in a comment',
                    'email' => true,
                    'push' => false,
                    'sms' => false,
                ],
                [
                    'key' => 'comments',
                    'title' => 'Comments',
                    'description' => 'Notify when another user comments your item',
                    'email' => true,
                    'push' => true,
                    'sms' => false,
                ],
                [
                    'key' => 'follows',
                    'title' => 'Follows',
                    'description' => 'Notify when another user follows you',
                    'email' => false,
                    'push' => true,
                    'sms' => false,
                ],
                [
                    'key' => 'new-device',
                    'title' => 'Log in from a new device',
                    'description' => 'Log in from a new device',
                    'email' => true,
                    'push' => true,
                    'sms' => true,
                ],
            ]);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString('c1000000-0000-1000-8000-000000000001'), $notificationConfiguration);

        $manager->persist($notificationConfiguration);
        $this->addReference('Configuration-profile-notification-settings', $notificationConfiguration);

        $appConfigurations = [
            'crm' => [
                [
                    'code' => 'crm-pipeline-default',
                    'keyName' => 'pipeline-default',
                    'status' => 'active',
                    'value' => ['stage' => 'lead', 'autoAssign' => true, 'currency' => 'EUR'],
                ],
                [
                    'code' => 'crm-notifications',
                    'keyName' => 'notifications',
                    'status' => 'active',
                    'value' => ['dailyDigest' => true, 'dealWonEmail' => true, 'slaWarningPush' => true],
                ],
                [
                    'code' => 'crm-views',
                    'keyName' => 'views',
                    'status' => 'active',
                    'value' => ['default' => 'kanban', 'compactCards' => false, 'showRevenue' => true],
                ],
            ],
            'shop' => [
                [
                    'code' => 'shop-catalog-defaults',
                    'keyName' => 'catalog-defaults',
                    'status' => 'active',
                    'value' => ['currency' => 'EUR', 'taxMode' => 'TTC', 'inventoryTracking' => true],
                ],
                [
                    'code' => 'shop-checkout',
                    'keyName' => 'checkout',
                    'status' => 'active',
                    'value' => ['guestCheckout' => false, 'minOrderAmount' => 20, 'allowCoupons' => true],
                ],
                [
                    'code' => 'shop-shipping',
                    'keyName' => 'shipping',
                    'status' => 'active',
                    'value' => ['defaultCarrier' => 'colissimo', 'freeOver' => 80, 'expediteEnabled' => true],
                ],
            ],
            'recruit' => [
                [
                    'code' => 'recruit-pipeline',
                    'keyName' => 'pipeline',
                    'status' => 'active',
                    'value' => ['defaultStep' => 'screening', 'autoArchiveDays' => 90, 'anonymizeRejected' => true],
                ],
                [
                    'code' => 'recruit-interviews',
                    'keyName' => 'interviews',
                    'status' => 'active',
                    'value' => ['timezone' => 'Europe/Paris', 'defaultDurationMinutes' => 45, 'reminderHours' => 24],
                ],
                [
                    'code' => 'recruit-offer',
                    'keyName' => 'offer',
                    'status' => 'active',
                    'value' => ['approvalRequired' => true, 'signatureProvider' => 'internal', 'offerExpiryDays' => 10],
                ],
            ],
            'school' => [
                [
                    'code' => 'school-calendar',
                    'keyName' => 'calendar',
                    'status' => 'inactive',
                    'value' => ['weekStartsOn' => 'monday', 'defaultView' => 'month', 'publicHolidays' => 'FR'],
                ],
                [
                    'code' => 'school-grading',
                    'keyName' => 'grading',
                    'status' => 'inactive',
                    'value' => ['scale' => 'A-F', 'passGrade' => 'C', 'roundHalfUp' => true],
                ],
            ],
        ];

        foreach ($appConfigurations as $application => $items) {
            foreach ($items as $index => $item) {
                $configuration = (new Configuration())
                    ->setCode($item['code'])
                    ->setKeyName('app-' . $application . '-' . $item['keyName'])
                    ->setStatus($item['status'])
                    ->setProfile($johnProfile)
                    ->setValue([
                        'application' => mb_strtoupper($application),
                        'owner' => 'john-root',
                        'scope' => 'user-application',
                        'settings' => $item['value'],
                    ]);

                $manager->persist($configuration);
                $this->addReference('Configuration-john-root-' . $application . '-' . ($index + 1), $configuration);
            }
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 11;
    }
}
