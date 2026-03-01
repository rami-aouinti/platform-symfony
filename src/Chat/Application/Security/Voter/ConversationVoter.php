<?php

declare(strict_types=1);

namespace App\Chat\Application\Security\Voter;

use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\Recruit\Domain\Enum\JobApplicationStatus;
use App\User\Application\Security\Permission;
use App\User\Application\Security\SecurityUser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

use function in_array;

/**
 * @extends Voter<string, mixed>
 * @package App\Chat\Application\Security\Voter
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class ConversationVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (
            !in_array($attribute, [
                Permission::CHAT_VIEW->value,
                Permission::CHAT_POST->value,
                Permission::CHAT_PARTICIPANT_MANAGE->value,
            ], true)
        ) {
            return false;
        }

        return $subject instanceof Conversation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof SecurityUser || !$subject instanceof Conversation) {
            return false;
        }

        $jobApplication = $subject->getJobApplication();
        if ($jobApplication === null || $jobApplication->getStatus() !== JobApplicationStatus::ACCEPTED) {
            return false;
        }

        $participant = $subject->getParticipants()->findFirst(
            static fn (int $_index, ConversationParticipant $participant): bool => $participant->getUser()?->getId() === $user->getUserIdentifier(),
        );

        if (!$participant instanceof ConversationParticipant) {
            return false;
        }

        if (
            $attribute === Permission::CHAT_POST->value
            || $attribute === Permission::CHAT_PARTICIPANT_MANAGE->value
        ) {
            return $this->isActiveParticipant($participant, $user->getUserIdentifier());
        }

        return true;
    }

    private function isActiveParticipant(ConversationParticipant $participant, string $userId): bool
    {
        return $participant->getUser()?->getId() === $userId;
    }
}
