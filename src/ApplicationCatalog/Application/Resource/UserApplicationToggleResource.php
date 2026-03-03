<?php

declare(strict_types=1);

namespace App\ApplicationCatalog\Application\Resource;

use App\ApplicationCatalog\Application\DTO\Application;
use App\ApplicationCatalog\Application\DTO\ApplicationMapper;
use App\ApplicationCatalog\Application\Resource\Interfaces\UserApplicationToggleResourceInterface;
use App\ApplicationCatalog\Application\Service\Interfaces\UserApplicationToggleServiceInterface;
use App\ApplicationCatalog\Domain\Entity\Application as ApplicationEntity;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use function in_array;

final readonly class UserApplicationToggleResource implements UserApplicationToggleResourceInterface
{
    public function __construct(
        private UserApplicationToggleServiceInterface $userApplicationToggleService,
        private UserTypeIdentification $userTypeIdentification,
        private ApplicationMapper $applicationMapper,
    ) {
    }

    public function toggle(User $targetUser, ApplicationEntity $application, bool $active): Application
    {
        $actor = $this->userTypeIdentification->getUser();

        if (!$actor instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        if ($actor->getId() !== $targetUser->getId() && !$this->isAdminLike($actor)) {
            throw new AccessDeniedHttpException('You cannot update application activations for another user.');
        }

        $userApplication = $this->userApplicationToggleService->toggle($targetUser, $application, $active);

        return $this->applicationMapper->mapEntityToDto($application, $userApplication);
    }

    private function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
