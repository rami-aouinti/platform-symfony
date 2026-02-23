<?php

declare(strict_types=1);

namespace App\User\Transport\AutoMapper\User;

use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\General\Transport\AutoMapper\RestRequestMapper;
use App\User\Application\DTO\User\Address;
use App\User\Application\DTO\User\UserAvatar;
use App\User\Application\DTO\User\UserProfile;
use App\User\Application\Resource\UserGroupResource;
use App\User\Domain\Entity\UserGroup;
use App\User\Domain\Enum\AddressType;
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

        if (isset($userProfile['phone'])) {
            $profile->setPhone($userProfile['phone']);
        }

        if (isset($userProfile['birthDate']) && is_string($userProfile['birthDate'])) {
            $profile->setBirthDate(new DateTimeImmutable($userProfile['birthDate']));
        }

        if (isset($userProfile['bio'])) {
            $profile->setBio($userProfile['bio']);
        }

        if (array_key_exists('contacts', $userProfile)) {
            $contacts = $userProfile['contacts'];
            $profile->setContacts(is_array($contacts) ? $contacts : null);
        }

        if (isset($userProfile['avatar']) && is_array($userProfile['avatar'])) {
            $profile->setAvatar($this->transformAvatar($userProfile['avatar']));
        }

        if (isset($userProfile['addresses']) && is_array($userProfile['addresses'])) {
            $profile->setAddresses($this->transformAddresses($userProfile['addresses']));
        }

        return $profile;
    }

    /**
     * @param array<string, mixed> $avatar
     */
    protected function transformAvatar(array $avatar): UserAvatar
    {
        $dto = new UserAvatar();

        if (array_key_exists('mediaId', $avatar)) {
            $dto->setMediaId(is_string($avatar['mediaId']) ? $avatar['mediaId'] : null);
        }

        if (isset($avatar['url']) && is_string($avatar['url'])) {
            $dto->setUrl($avatar['url']);
        }

        return $dto;
    }

    /**
     * @param array<int, mixed> $addresses
     *
     * @return array<int, Address>
     */
    protected function transformAddresses(array $addresses): array
    {
        return array_map(function (mixed $item): Address {
            if (!is_array($item)) {
                throw new InvalidArgumentException('Invalid address payload.');
            }

            $typeRaw = $item['type'] ?? null;
            if (!is_string($typeRaw)) {
                throw new InvalidArgumentException('Address type must be a string.');
            }

            $dto = (new Address())
                ->setType(AddressType::tryFrom($typeRaw) ?? throw new InvalidArgumentException('Invalid address type.'))
                ->setStreetLine1((string) ($item['streetLine1'] ?? ''))
                ->setStreetLine2(isset($item['streetLine2']) ? (string) $item['streetLine2'] : null)
                ->setPostalCode((string) ($item['postalCode'] ?? ''))
                ->setCity((string) ($item['city'] ?? ''))
                ->setState(isset($item['state']) ? (string) $item['state'] : null)
                ->setCountryCode((string) ($item['countryCode'] ?? ''));

            return $dto;
        }, $addresses);
    }
}
