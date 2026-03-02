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

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 11;
    }
}
