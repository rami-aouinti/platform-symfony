<?php

declare(strict_types=1);

namespace App\PluginCatalog\Application\Resource;

use App\ApplicationCatalog\Domain\Entity\UserApplication;
use App\PluginCatalog\Application\DTO\Plugin;
use App\PluginCatalog\Application\DTO\PluginMapper;
use App\PluginCatalog\Application\Resource\Interfaces\UserApplicationPluginToggleResourceInterface;
use App\PluginCatalog\Application\Service\Interfaces\UserApplicationPluginToggleServiceInterface;
use App\PluginCatalog\Domain\Entity\Plugin as PluginEntity;
use App\User\Application\Security\UserTypeIdentification;
use App\User\Domain\Entity\User;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use function in_array;

final readonly class UserApplicationPluginToggleResource implements UserApplicationPluginToggleResourceInterface
{
    public function __construct(
        private UserApplicationPluginToggleServiceInterface $userApplicationPluginToggleService,
        private UserTypeIdentification $userTypeIdentification,
        private PluginMapper $pluginMapper,
    ) {
    }

    public function attach(UserApplication $userApplication, PluginEntity $plugin): Plugin
    {
        return $this->toggle($userApplication, $plugin, true);
    }

    public function toggle(UserApplication $userApplication, PluginEntity $plugin, bool $active): Plugin
    {
        $actor = $this->userTypeIdentification->getUser();

        if (!$actor instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        if ($actor->getId() !== $userApplication->getUser()->getId() && !$this->isAdminLike($actor)) {
            throw new AccessDeniedHttpException('You cannot update plugin activations for another user application.');
        }

        if (!$userApplication->isActive()) {
            throw new AccessDeniedHttpException('Application must be active before toggling plugins.');
        }

        $userApplicationPlugin = $this->userApplicationPluginToggleService->toggle($userApplication, $plugin, $active);

        return $this->pluginMapper->mapEntityToDto($plugin, $userApplicationPlugin);
    }

    public function detach(UserApplication $userApplication, PluginEntity $plugin): void
    {
        $actor = $this->userTypeIdentification->getUser();

        if (!$actor instanceof User) {
            throw new AccessDeniedHttpException('Authenticated user not found.');
        }

        if ($actor->getId() !== $userApplication->getUser()->getId() && !$this->isAdminLike($actor)) {
            throw new AccessDeniedHttpException('You cannot update plugin activations for another user application.');
        }

        $this->userApplicationPluginToggleService->detach($userApplication, $plugin);
    }

    private function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }
}
