<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\DataFixtures\ORM;

use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\ChatMessageReaction;
use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\General\Domain\Rest\UuidHelper;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadChatMessageData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $johnRoot */
        $johnRoot = $this->getReference('User-john-root', User::class);
        /** @var User $carolUser */
        $carolUser = $this->getReference('User-carol-user', User::class);
        /** @var User $hugoUser */
        $hugoUser = $this->getReference('User-hugo-user', User::class);

        $conversation1 = $this->createConversation(
            '71000000-0000-1000-8000-000000000001',
            [
                ['71000000-0000-1000-8000-000000000002', $johnRoot],
                ['71000000-0000-1000-8000-000000000003', $carolUser],
            ],
        );

        $message1 = $this->createMessage(
            '71000000-0000-1000-8000-000000000004',
            $conversation1,
            $johnRoot,
            'Hello Carol, thank you for applying. Could you share your availability this week?',
            [
                ['type' => 'image', 'url' => 'https://example.test/chat/schedule.png', 'name' => 'schedule.png', 'mimeType' => 'image/png'],
            ],
            new DateTimeImmutable('-2 days'),
        );

        $message2 = $this->createMessage(
            '71000000-0000-1000-8000-000000000005',
            $conversation1,
            $carolUser,
            'Hi John, Thursday afternoon works for me. I also attached my portfolio.',
            [
                ['type' => 'file', 'url' => 'https://example.test/chat/portfolio.pdf', 'name' => 'portfolio.pdf', 'mimeType' => 'application/pdf'],
            ],
            null,
        );

        $conversation2 = $this->createConversation(
            '71000000-0000-1000-8000-000000000006',
            [
                ['71000000-0000-1000-8000-000000000007', $johnRoot],
                ['71000000-0000-1000-8000-000000000008', $hugoUser],
                ['71000000-0000-1000-8000-000000000009', $carolUser],
            ],
        );

        $message3 = $this->createMessage(
            '71000000-0000-1000-8000-000000000010',
            $conversation2,
            $hugoUser,
            'Team sync tomorrow at 10:00, please confirm.',
            [],
            new DateTimeImmutable('-1 day'),
        );

        $message4 = $this->createMessage(
            '71000000-0000-1000-8000-000000000011',
            $conversation2,
            $johnRoot,
            'Confirmed ✅',
            [],
            new DateTimeImmutable('-20 hours'),
        );

        $reactions = [
            $this->createReaction('71000000-0000-1000-8000-000000000012', $message1, $carolUser, 'heart'),
            $this->createReaction('71000000-0000-1000-8000-000000000013', $message1, $johnRoot, 'thumbs_up'),
            $this->createReaction('71000000-0000-1000-8000-000000000014', $message2, $johnRoot, 'surprised'),
            $this->createReaction('71000000-0000-1000-8000-000000000015', $message3, $carolUser, 'sad'),
            $this->createReaction('71000000-0000-1000-8000-000000000016', $message4, $hugoUser, 'heart'),
        ];

        foreach ([$conversation1, $conversation2, $message1, $message2, $message3, $message4, ...$reactions] as $entity) {
            $manager->persist($entity);
        }

        foreach ($conversation1->getParticipants() as $participant) {
            $manager->persist($participant);
        }
        foreach ($conversation2->getParticipants() as $participant) {
            $manager->persist($participant);
        }

        $manager->flush();
    }

    private function createConversation(string $id, array $participants): Conversation
    {
        $conversation = new Conversation();
        PhpUnitUtil::setProperty('id', UuidHelper::fromString($id), $conversation);

        foreach ($participants as [$participantId, $user]) {
            $participant = (new ConversationParticipant())
                ->setConversation($conversation)
                ->setUser($user);
            PhpUnitUtil::setProperty('id', UuidHelper::fromString($participantId), $participant);
            $conversation->addParticipant($participant);
        }

        return $conversation;
    }

    private function createMessage(
        string $id,
        Conversation $conversation,
        User $sender,
        string $content,
        array $attachments,
        ?DateTimeImmutable $readAt,
    ): ChatMessage {
        $message = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($sender)
            ->setContent($content)
            ->setAttachments($attachments)
            ->setReadAt($readAt);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString($id), $message);

        return $message;
    }

    private function createReaction(string $id, ChatMessage $message, User $user, string $reaction): ChatMessageReaction
    {
        $entity = (new ChatMessageReaction())
            ->setMessage($message)
            ->setUser($user)
            ->setReaction($reaction);

        PhpUnitUtil::setProperty('id', UuidHelper::fromString($id), $entity);

        return $entity;
    }

    #[Override]
    public function getOrder(): int
    {
        return 10;
    }
}
