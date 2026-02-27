<?php

declare(strict_types=1);

namespace App\Calendar\Transport\AutoMapper\Event;

use App\General\Transport\AutoMapper\RestRequestMapper;
use App\General\Transport\AutoMapper\PropertiesConventionTrait;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Throwable;

class RequestMapper extends RestRequestMapper
{
    use PropertiesConventionTrait;

    public function __construct(
        private readonly UserResource $userResource,
    ) {
    }

    private const PROPERTIES = [
        'title',
        'description',
        'location',
        'user',
        'startAt',
        'endAt',
        'isAllDay',
        'timezone',
        'status',
        'visibility',
        'isCancelled',
        'url',
        'color',
        'backgroundColor',
        'borderColor',
        'textColor',
        'organizerName',
        'organizerEmail',
        'attendees',
        'rrule',
        'recurrenceExceptions',
        'recurrenceEndAt',
        'recurrenceCount',
        'reminders',
        'metadata',
    ];


    protected function transformUser(?string $user): ?User
    {
        if ($user === null || $user === '') {
            return null;
        }

        try {
            return $this->userResource->getReference($user);
        } catch (Throwable) {
            return null;
        }
    }

    protected function transformStartAt(?string $value): ?DateTimeImmutable
    {
        return $value !== null && $value !== '' ? new DateTimeImmutable($value) : null;
    }

    protected function transformEndAt(?string $value): ?DateTimeImmutable
    {
        return $value !== null && $value !== '' ? new DateTimeImmutable($value) : null;
    }

    protected function transformRecurrenceEndAt(?string $value): ?DateTimeImmutable
    {
        return $value !== null && $value !== '' ? new DateTimeImmutable($value) : null;
    }
}
