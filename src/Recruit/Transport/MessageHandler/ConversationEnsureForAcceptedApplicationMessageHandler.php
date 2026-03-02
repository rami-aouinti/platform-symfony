<?php

declare(strict_types=1);

namespace App\Recruit\Transport\MessageHandler;

use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationRepositoryInterface;
use App\Recruit\Domain\Message\ConversationEnsureForAcceptedApplicationMessage;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @package App\Recruit\Transport\MessageHandler
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
#[AsMessageHandler]
readonly class ConversationEnsureForAcceptedApplicationMessageHandler
{
    public function __construct(
        private ConversationRepositoryInterface $conversationRepository,
        private ConversationParticipantRepositoryInterface $participantRepository,
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(ConversationEnsureForAcceptedApplicationMessage $message, User $sender, User $receiver): void
    {
        $conversation = (new Conversation());

        $candidateParticipant = new ConversationParticipant()
            ->setConversation($conversation)
            ->setUser($sender);

        $ownerParticipant = new ConversationParticipant()
            ->setConversation($conversation)
            ->setUser($receiver);

        try {
            $this->conversationRepository->save($conversation, false);
            $this->participantRepository->save($candidateParticipant, false);
            $this->participantRepository->save($ownerParticipant);
        } catch (UniqueConstraintViolationException) {
            // Idempotence guard for concurrent/reprocessed messages.
        }
    }
}
