<?php

declare(strict_types=1);

namespace App\User\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Domain\Rest\UuidHelper;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\Address;
use App\User\Domain\Entity\SocialAccount;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserAvatar;
use App\User\Domain\Entity\UserGroup;
use App\User\Domain\Enum\AddressType;
use App\User\Domain\Enum\SocialProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;
use Throwable;

use function array_map;
use function str_replace;

/**
 * @package App\User
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class LoadUserData extends Fixture implements OrderedFixtureInterface
{
    /**
     * @var array<non-empty-string, non-empty-string>
     */
    public static array $uuids = [
        'john' => '20000000-0000-1000-8000-000000000001',
        'john-logged' => '20000000-0000-1000-8000-000000000002',
        'john-api' => '20000000-0000-1000-8000-000000000003',
        'john-user' => '20000000-0000-1000-8000-000000000004',
        'john-admin' => '20000000-0000-1000-8000-000000000005',
        'john-root' => '20000000-0000-1000-8000-000000000006',
        'alice-user' => '20000000-0000-1000-8000-000000000007',
        'bob-admin' => '20000000-0000-1000-8000-000000000008',
        'carol-user' => '20000000-0000-1000-8000-000000000009',
    ];

    public function __construct(
        private readonly RolesServiceInterface $rolesService,
    ) {
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @throws Throwable
     */
    #[Override]
    public function load(ObjectManager $manager): void
    {
        array_map(
            fn (?string $role): bool => $this->createRoleUser($manager, $role),
            [
                null,
                ...$this->rolesService->getRoles(),
            ],
        );

        $this->createNamedUser($manager, 'alice', 'user', [
            [SocialProvider::GOOGLE, 'alice-google-id'],
            [SocialProvider::GITHUB, 'alice-github-id'],
            [SocialProvider::LINKEDIN, 'alice-linkedin-id'],
        ]);
        $this->createNamedUser($manager, 'bob', 'admin', [
            [SocialProvider::AZURE, 'bob-azure-id'],
            [SocialProvider::GITLAB, 'bob-gitlab-id'],
        ]);
        $this->createNamedUser($manager, 'carol', 'user', [
            [SocialProvider::FACEBOOK, 'carol-facebook-id'],
            [SocialProvider::INSTAGRAM, 'carol-instagram-id'],
        ]);

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 3;
    }

    public static function getUuidByKey(string $key): string
    {
        return self::$uuids[$key];
    }

    /**
     * @throws Throwable
     */
    private function createRoleUser(ObjectManager $manager, ?string $role = null): true
    {
        $suffix = $role === null ? '' : '-' . $this->rolesService->getShort($role);

        $entity = new User()
            ->setUsername('john' . $suffix)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setEmail('john.doe' . $suffix . '@test.com')
            ->setLanguage(Language::EN)
            ->setLocale(Locale::EN)
            ->setPlainPassword('password' . $suffix);

        if ($role === null) {
            $this->fillProfile($entity, '+33123456789', 'Demo profile for fixtures');

            $entity->addSocialAccount(
                (new SocialAccount($entity, SocialProvider::GOOGLE, 'john-google-id'))
                    ->setProviderEmail('john.doe@test.com'),
            );
            $entity->addSocialAccount(
                (new SocialAccount($entity, SocialProvider::GITHUB, 'john-github-id'))
                    ->setProviderEmail('john.doe@test.com'),
            );
        }

        if ($role !== null) {
            /** @var UserGroup $userGroup */
            $userGroup = $this->getReference('UserGroup-' . $this->rolesService->getShort($role), UserGroup::class);
            $entity->addUserGroup($userGroup);
        }

        PhpUnitUtil::setProperty('id', UuidHelper::fromString(self::$uuids['john' . $suffix]), $entity);

        $manager->persist($entity);
        $this->addReference('User-' . $entity->getUsername(), $entity);

        return true;
    }

    /**
     * @param array<int, array{0: SocialProvider, 1: string}> $socialAccounts
     *
     * @throws Throwable
     */
    private function createNamedUser(ObjectManager $manager, string $name, string $roleShort, array $socialAccounts): void
    {
        $entity = new User()
            ->setUsername($name . '-' . $roleShort)
            ->setFirstName(ucfirst($name))
            ->setLastName('Fixture')
            ->setEmail($name . '.' . $roleShort . '@test.com')
            ->setLanguage(Language::EN)
            ->setLocale(Locale::EN)
            ->setPlainPassword('password-' . $roleShort);

        /** @var UserGroup $userGroup */
        $userGroup = $this->getReference('UserGroup-' . $roleShort, UserGroup::class);
        $entity->addUserGroup($userGroup);

        $this->fillProfile($entity, '+33000000000', 'Fixture account for social auth tests: ' . $name);

        foreach ($socialAccounts as [$provider, $providerUserId]) {
            $entity->addSocialAccount(
                (new SocialAccount($entity, $provider, $providerUserId))
                    ->setProviderEmail($entity->getEmail()),
            );
        }

        $uuidKey = $name . '-' . $roleShort;
        PhpUnitUtil::setProperty('id', UuidHelper::fromString(self::$uuids[$uuidKey]), $entity);

        $manager->persist($entity);
        $this->addReference('User-' . str_replace('.', '-', $entity->getUsername()), $entity);
    }

    private function fillProfile(User $entity, string $phone, string $bio): void
    {
        $profile = $entity->getOrCreateUserProfile()
            ->setPhone($phone)
            ->setBio($bio);

        $profile->setAvatar(
            (new UserAvatar($profile))
                ->setUrl('https://example.test/avatar.jpg')
                ->setMediaId('demo-media-id'),
        );

        $profile->addAddress(
            (new Address())
                ->setType(AddressType::HOME)
                ->setStreetLine1('1 Demo Street')
                ->setPostalCode('75001')
                ->setCity('Paris')
                ->setCountryCode('FR'),
        );
    }
}
