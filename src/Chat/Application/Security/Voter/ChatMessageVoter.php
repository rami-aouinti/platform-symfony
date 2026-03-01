<?php

declare(strict_types=1);

namespace App\Chat\Application\Security\Voter;

use App\Chat\Domain\Entity\ChatMessage;
use App\User\Application\Security\Permission;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

/**
 * @extends Voter<string, mixed>
 * @package App\Chat\Application\Security\Voter
 */
class ChatMessageVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            Permission::CHAT_EDIT->value,
            Permission::CHAT_DELETE->value,
        ], true)) {
            return false;
        }

        return $subject instanceof ChatMessage;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser || !$subject instanceof ChatMessage) {
            return false;
        }

        return $subject->getSender()?->getId() === $user->getUserIdentifier();
    }
}
