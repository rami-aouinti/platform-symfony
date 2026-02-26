<?php

declare(strict_types=1);

namespace App\Tests\Application\JobApplication\Transport\MessageHandler;

use App\Chat\Infrastructure\Repository\ConversationRepository;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\JobApplication\Domain\Message\ConversationEnsureForAcceptedApplicationMessage;
use App\JobApplication\Infrastructure\Repository\JobApplicationRepository;
use App\JobApplication\Transport\MessageHandler\ConversationEnsureForAcceptedApplicationMessageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConversationEnsureForAcceptedApplicationMessageHandlerTest extends KernelTestCase
{
    private const string PENDING_APPLICATION_ID = '50000000-0000-1000-8000-000000000001';
    private const string ACCEPTED_APPLICATION_ID = '50000000-0000-1000-8000-000000000002';
    private const string REJECTED_APPLICATION_ID = '50000000-0000-1000-8000-000000000003';
    private const string WITHDRAWN_APPLICATION_ID = '50000000-0000-1000-8000-000000000004';

    private ConversationEnsureForAcceptedApplicationMessageHandler $handler;
    private JobApplicationRepository $jobApplicationRepository;
    private ConversationRepository $conversationRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = static::getContainer()->get(ConversationEnsureForAcceptedApplicationMessageHandler::class);
        $this->jobApplicationRepository = static::getContainer()->get(JobApplicationRepository::class);
        $this->conversationRepository = static::getContainer()->get(ConversationRepository::class);
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testCreatesConversationOnFirstAcceptance(): void
    {
        $application = $this->jobApplicationRepository->find(self::PENDING_APPLICATION_ID);
        self::assertNotNull($application);

        $this->removeConversationIfAny(self::PENDING_APPLICATION_ID);

        $application->setStatus(JobApplicationStatus::ACCEPTED);
        $this->entityManager->flush();

        ($this->handler)(new ConversationEnsureForAcceptedApplicationMessage(self::PENDING_APPLICATION_ID));

        $conversation = $this->conversationRepository->findOneByJobApplicationId(self::PENDING_APPLICATION_ID);
        self::assertNotNull($conversation);
        self::assertCount(2, $conversation->getParticipants());
    }

    public function testDoesNotCreateConversationForRejectedOrWithdrawnStatus(): void
    {
        $this->removeConversationIfAny(self::REJECTED_APPLICATION_ID);
        $this->removeConversationIfAny(self::WITHDRAWN_APPLICATION_ID);

        ($this->handler)(new ConversationEnsureForAcceptedApplicationMessage(self::REJECTED_APPLICATION_ID));
        ($this->handler)(new ConversationEnsureForAcceptedApplicationMessage(self::WITHDRAWN_APPLICATION_ID));

        self::assertNull($this->conversationRepository->findOneByJobApplicationId(self::REJECTED_APPLICATION_ID));
        self::assertNull($this->conversationRepository->findOneByJobApplicationId(self::WITHDRAWN_APPLICATION_ID));
    }

    public function testMessageReprocessingIsIdempotent(): void
    {
        $this->removeConversationIfAny(self::ACCEPTED_APPLICATION_ID);

        ($this->handler)(new ConversationEnsureForAcceptedApplicationMessage(self::ACCEPTED_APPLICATION_ID));
        $firstConversation = $this->conversationRepository->findOneByJobApplicationId(self::ACCEPTED_APPLICATION_ID);

        self::assertNotNull($firstConversation);
        $firstConversationId = $firstConversation->getId();

        ($this->handler)(new ConversationEnsureForAcceptedApplicationMessage(self::ACCEPTED_APPLICATION_ID));
        $secondConversation = $this->conversationRepository->findOneByJobApplicationId(self::ACCEPTED_APPLICATION_ID);

        self::assertNotNull($secondConversation);
        self::assertSame($firstConversationId, $secondConversation->getId());
        self::assertCount(2, $secondConversation->getParticipants());
    }

    private function removeConversationIfAny(string $applicationId): void
    {
        $conversation = $this->conversationRepository->findOneByJobApplicationId($applicationId);

        if ($conversation === null) {
            return;
        }

        $this->entityManager->remove($conversation);
        $this->entityManager->flush();
    }
}
