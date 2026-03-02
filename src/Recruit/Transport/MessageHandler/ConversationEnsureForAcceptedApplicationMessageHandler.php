<?php

declare(strict_types=1);

namespace App\Recruit\Transport\MessageHandler;

use App\Chat\Application\Service\Realtime\Interfaces\ChatRealtimePublisherInterface;
use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\Chat\Domain\Repository\Interfaces\ChatMessageRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\Chat\Domain\Repository\Interfaces\ConversationRepositoryInterface;
use App\Recruit\Domain\Message\ConversationEnsureForAcceptedApplicationMessage;
use App\User\Domain\Entity\User;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
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
        private ChatMessageRepositoryInterface $chatMessageRepository,
        private ChatRealtimePublisherInterface $chatRealtimePublisher,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function __invoke(ConversationEnsureForAcceptedApplicationMessage $message): void
    {
        $sender = $this->userRepository->find($message->senderUserId);
        $receiver = $this->userRepository->find($message->receiverUserId);

        if (!$sender instanceof User || !$receiver instanceof User) {
            return;
        }

        $conversation = (new Conversation());

        $candidateParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($sender);

        $ownerParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($receiver);

        try {
            $this->conversationRepository->save($conversation, false);
            $this->participantRepository->save($candidateParticipant, false);
            $this->participantRepository->save($ownerParticipant, false);

            $chatMessage = (new ChatMessage())
                ->setConversation($conversation)
                ->setSender($sender)
                ->setContent('Votre candidature a été acceptée. Vous pouvez échanger ici avec le recruteur.');

            $this->chatMessageRepository->save($chatMessage);
            $this->chatRealtimePublisher->publish($chatMessage);
        } catch (UniqueConstraintViolationException) {
            // Idempotence guard for concurrent/reprocessed messages.
        }
    }
}
