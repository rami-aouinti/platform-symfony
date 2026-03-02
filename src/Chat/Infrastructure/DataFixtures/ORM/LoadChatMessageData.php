<?php

declare(strict_types=1);

namespace App\Chat\Infrastructure\DataFixtures\ORM;

use App\Chat\Domain\Entity\ChatMessage;
use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\General\Domain\Rest\UuidHelper;
use App\Recruit\Domain\Entity\JobApplication;
use App\Tests\Utils\PhpUnitUtil;
use App\User\Domain\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

/**
 * @package App\Chat\Infrastructure\DataFixtures\ORM
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
final class LoadChatMessageData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        /** @var User $johnRoot */
        $johnRoot = $this->getReference('User-john-root', User::class);
        /** @var User $carolUser */
        $carolUser = $this->getReference('User-carol-user', User::class);
        /** @var JobApplication $jobApplication */
        $jobApplication = $this->getReference('JobApplication-carol-on-php-backend-engineer', JobApplication::class);

        $conversation = (new Conversation())
            ->setJobApplication($jobApplication);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('71000000-0000-1000-8000-000000000001'), $conversation);

        $johnParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($johnRoot);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('71000000-0000-1000-8000-000000000002'), $johnParticipant);

        $carolParticipant = (new ConversationParticipant())
            ->setConversation($conversation)
            ->setUser($carolUser);
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('71000000-0000-1000-8000-000000000003'), $carolParticipant);

        $message1 = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($johnRoot)
            ->setContent('Hello Carol, thank you for applying. Could you share your availability this week?');
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('71000000-0000-1000-8000-000000000004'), $message1);

        $message2 = (new ChatMessage())
            ->setConversation($conversation)
            ->setSender($johnRoot)
            ->setContent('We can schedule a technical call on Thursday afternoon if that works for you.');
        PhpUnitUtil::setProperty('id', UuidHelper::fromString('71000000-0000-1000-8000-000000000005'), $message2);

        foreach ([$conversation, $johnParticipant, $carolParticipant, $message1, $message2] as $entity) {
            $manager->persist($entity);
        }

        $manager->flush();

        $this->addReference('Conversation-john-root-carol-php-backend', $conversation);
        $this->addReference('ConversationParticipant-john-root-carol-php-backend', $johnParticipant);
        $this->addReference('ConversationParticipant-carol-user-php-backend', $carolParticipant);
        $this->addReference('ChatMessage-john-root-php-backend-1', $message1);
        $this->addReference('ChatMessage-john-root-php-backend-2', $message2);
    }

    #[Override]
    public function getOrder(): int
    {
        return 10;
    }
}
