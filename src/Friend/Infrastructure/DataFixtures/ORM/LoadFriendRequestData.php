<?php

declare(strict_types=1);

namespace App\Friend\Infrastructure\DataFixtures\ORM;

use App\Friend\Domain\Entity\FriendRequest;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadFriendRequestData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $johnRoot */
        $johnRoot = $this->getReference('User-john-root', User::class);
        /** @var User $alice */
        $alice = $this->getReference('User-alice-user', User::class);
        /** @var User $bob */
        $bob = $this->getReference('User-bob-admin', User::class);
        /** @var User $carol */
        $carol = $this->getReference('User-carol-user', User::class);
        /** @var User $dave */
        $dave = $this->getReference('User-dave-user', User::class);
        /** @var User $emma */
        $emma = $this->getReference('User-emma-user', User::class);
        /** @var User $frank */
        $frank = $this->getReference('User-frank-admin', User::class);

        $items = [
            'FriendRequest-john-alice-accepted' => (new FriendRequest($johnRoot, $alice))->accept(),
            'FriendRequest-john-bob-accepted' => (new FriendRequest($johnRoot, $bob))->accept(),
            'FriendRequest-carol-john-accepted' => (new FriendRequest($carol, $johnRoot))->accept(),
            'FriendRequest-john-dave-accepted' => (new FriendRequest($johnRoot, $dave))->accept(),
            'FriendRequest-emma-john-pending' => new FriendRequest($emma, $johnRoot),
            'FriendRequest-john-frank-pending' => new FriendRequest($johnRoot, $frank),
        ];

        $uuids = [
            'FriendRequest-john-alice-accepted' => '81000000-0000-1000-8000-000000000001',
            'FriendRequest-john-bob-accepted' => '81000000-0000-1000-8000-000000000002',
            'FriendRequest-carol-john-accepted' => '81000000-0000-1000-8000-000000000003',
            'FriendRequest-john-dave-accepted' => '81000000-0000-1000-8000-000000000004',
            'FriendRequest-emma-john-pending' => '81000000-0000-1000-8000-000000000005',
            'FriendRequest-john-frank-pending' => '81000000-0000-1000-8000-000000000006',
        ];

        foreach ($items as $reference => $friendRequest) {
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuids[$reference]), $friendRequest);
            $manager->persist($friendRequest);
            $this->addReference($reference, $friendRequest);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 10;
    }
}
