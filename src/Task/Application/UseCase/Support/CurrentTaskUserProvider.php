<?php

declare(strict_types=1);

namespace App\Task\Application\UseCase\Support;

use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * CurrentTaskUserProvider.
 *
 * @package App\Task\Application\UseCase\Support
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
final class CurrentTaskUserProvider
{
    public function __construct(
        private readonly UserTypeIdentification $userTypeIdentification,
    ) {
    }

    public function getCurrentUser(): User
    {
        $user = $this->userTypeIdentification->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        return $user;
    }
}
