<?php

declare(strict_types=1);

namespace App\Recruit\Application\Security\Voter;

use App\Recruit\Domain\Entity\Resume;
use App\Role\Domain\Enum\Role;
use App\User\Application\Security\Permission;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

/**
 * @extends Voter<string, mixed>
 * @package App\Recruit\Application\Security\Voter
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ResumeVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (
            !in_array($attribute, [
                Permission::RESUME_VIEW->value,
                Permission::RESUME_EDIT->value,
                Permission::RESUME_DELETE->value,
                Permission::RESUME_USE_FOR_APPLICATION->value,
            ], true)
        ) {
            return false;
        }

        return $subject instanceof Resume;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser || !$subject instanceof Resume) {
            return false;
        }

        if ($this->hasGlobalGrant($user)) {
            return true;
        }

        $isOwner = $subject->getOwner()?->getId() === $user->getUserIdentifier();

        if ($attribute === Permission::RESUME_VIEW->value) {
            return $isOwner || $subject->isPublic();
        }

        return $isOwner;
    }

    private function hasGlobalGrant(SecurityUser $user): bool
    {
        return in_array(Role::ROOT->value, $user->getRoles(), true)
            || in_array(Role::ADMIN->value, $user->getRoles(), true);
    }
}
