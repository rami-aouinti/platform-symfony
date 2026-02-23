<?php

declare(strict_types=1);

namespace App\User\Transport\AutoMapper\User;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Transport\AutoMapper\RestRequestMapper;
use App\User\Application\DTO\User\UserProfile;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use DateTimeImmutable;
use InvalidArgumentException;
use Throwable;

use function array_map;

/**
 * @package App\User
 */
class RequestMapper extends RestRequestMapper
{
    /**
     * @var array<int, non-empty-string>
     */
    protected static array $properties = [
        'username',
        'firstName',
        'lastName',
        'email',
        'language',
        'locale',
        'timezone',
        'userProfile',
        'userGroups',
        'password',
    ];

    public function __construct(
        private readonly UserGroupResource $userGroupResource,
    ) {
    }

    /**
     * @param array<int, string> $userGroups
     *
     * @return array<int, UserGroup>
     *
     * @throws Throwable
     */
    protected function transformUserGroups(array $userGroups): array
    {
        return array_map(
            fn (string $userGroupUuid): UserGroup => $this->userGroupResource->getReference($userGroupUuid),
            $userGroups,
        );
    }

    protected function transformLanguage(string $language): Language
    {
        return Language::tryFrom($language) ?? throw new InvalidArgumentException('Invalid language');
    }

    protected function transformLocale(string $locale): Locale
    {
        return Locale::tryFrom($locale) ?? throw new InvalidArgumentException('Invalid locale');
    }

    /**
     * @param array<string, mixed> $userProfile
     */
    protected function transformUserProfile(array $userProfile): UserProfile
    {
        $profile = new UserProfile();

        if (isset($userProfile['photo'])) {
            $profile->setPhoto($userProfile['photo']);
        }

        if (isset($userProfile['phone'])) {
            $profile->setPhone($userProfile['phone']);
        }

        if (isset($userProfile['birthDate']) && is_string($userProfile['birthDate'])) {
            $profile->setBirthDate(new DateTimeImmutable($userProfile['birthDate']));
        }

        if (isset($userProfile['bio'])) {
            $profile->setBio($userProfile['bio']);
        }

        if (isset($userProfile['address'])) {
            $profile->setAddress($userProfile['address']);
        }

        if (array_key_exists('contacts', $userProfile)) {
            $contacts = $userProfile['contacts'];
            $profile->setContacts(is_array($contacts) ? $contacts : null);
        }

        return $profile;
    }
}
