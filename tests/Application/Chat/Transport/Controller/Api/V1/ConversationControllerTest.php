<?php

declare(strict_types=1);

namespace App\Tests\Application\Chat\Transport\Controller\Api\V1;

use App\Chat\Domain\Entity\Conversation;
use App\Chat\Domain\Entity\ConversationParticipant;
use App\General\Domain\Utils\JSON;
use App\JobApplication\Domain\Entity\JobApplication;
use App\Tests\TestCase\WebTestCase;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ConversationControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/chat/conversations';
    private const string ACCEPTED_APPLICATION_ID = '50000000-0000-1000-8000-000000000002';
    private const string WITHDRAWN_APPLICATION_ID = '50000000-0000-1000-8000-000000000004';

    private string $acceptedConversationId;
    private string $withdrawnConversationId;

    protected function setUp(): void
    {
        parent::setUp();

        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $this->acceptedConversationId = $this->ensureConversation(
            $entityManager,
            self::ACCEPTED_APPLICATION_ID,
            ['hugo-user', 'john-user'],
        );

        $this->withdrawnConversationId = $this->ensureConversation(
            $entityManager,
            self::WITHDRAWN_APPLICATION_ID,
            ['hugo-user', 'john-user'],
        );
    }

    /**
     * @throws Throwable
     */
    public function testParticipantCanListAndAccessConversation(): void
    {
        $client = $this->getTestClient('hugo-user', 'password-user');

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $list = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($list);

        $conversationIds = array_map(static fn (array $row): string => (string)($row['id'] ?? ''), $list);

        self::assertContains($this->acceptedConversationId, $conversationIds);
        self::assertNotContains($this->withdrawnConversationId, $conversationIds);

        $client->request('GET', self::BASE_URL . '/' . $this->acceptedConversationId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    public function testNonParticipantIsForbiddenOnConversationEndpoints(): void
    {
        $client = $this->getTestClient('bob-admin', 'password-admin');

        $client->request('GET', self::BASE_URL . '/' . $this->acceptedConversationId);
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            self::BASE_URL . '/' . $this->acceptedConversationId . '/messages',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: JSON::encode([
                'content' => 'Message refusé',
            ]),
        );
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    public function testPostingMessageRequiresAcceptedApplicationConversation(): void
    {
        $client = $this->getTestClient('hugo-user', 'password-user');

        $client->request(
            'POST',
            self::BASE_URL . '/' . $this->acceptedConversationId . '/messages',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: JSON::encode([
                'content' => 'Bonjour, je suis disponible pour un échange.',
            ]),
        );
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            self::BASE_URL . '/' . $this->withdrawnConversationId . '/messages',
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: JSON::encode([
                'content' => 'Ce message doit être refusé.',
            ]),
        );
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    private function ensureConversation(EntityManagerInterface $entityManager, string $applicationId, array $participantUsernames): string
    {
        $application = $entityManager->getRepository(JobApplication::class)->find($applicationId);
        self::assertInstanceOf(JobApplication::class, $application);

        $existing = $entityManager->getRepository(Conversation::class)->findOneBy([
            'jobApplication' => $application,
        ]);
        if ($existing instanceof Conversation) {
            return $existing->getId();
        }

        $conversation = (new Conversation())->setJobApplication($application);
        $entityManager->persist($conversation);

        foreach ($participantUsernames as $username) {
            $user = $entityManager->getRepository(User::class)->findOneBy([
                'username' => $username,
            ]);
            self::assertInstanceOf(User::class, $user);

            $participant = (new ConversationParticipant())
                ->setConversation($conversation)
                ->setUser($user);

            $entityManager->persist($participant);
            $conversation->addParticipant($participant);
        }

        $entityManager->flush();

        return $conversation->getId();
    }
}
