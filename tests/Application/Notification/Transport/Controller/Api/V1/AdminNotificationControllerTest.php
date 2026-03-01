<?php

declare(strict_types=1);

namespace App\Tests\Application\Notification\Transport\Controller\Api\V1;

use App\Tests\TestCase\WebTestCase;
use App\User\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class AdminNotificationControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/admin/notifications/users';

    public function testAdminCanReadUnreadCountForAnyUser(): void
    {
        $userId = $this->getUserIdByUsername('hugo-user');
        $client = $this->getTestClient('bob-admin', 'password-admin');

        $client->request('GET', self::BASE_URL . '/' . $userId . '/unread-count');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertJson((string) $client->getResponse()->getContent());
    }

    public function testNonAdminGetsForbidden(): void
    {
        $userId = $this->getUserIdByUsername('hugo-user');
        $client = $this->getTestClient('hugo-user', 'password-user');

        $client->request('GET', self::BASE_URL . '/' . $userId . '/unread-count');

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUnknownUserReturnsNotFound(): void
    {
        $client = $this->getTestClient('bob-admin', 'password-admin');

        $client->request('GET', self::BASE_URL . '/00000000-0000-1000-8000-000000000099/unread-count');

        self::assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    private function getUserIdByUsername(string $username): string
    {
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'username' => $username,
        ]);

        self::assertInstanceOf(User::class, $user);

        return $user->getId();
    }
}
