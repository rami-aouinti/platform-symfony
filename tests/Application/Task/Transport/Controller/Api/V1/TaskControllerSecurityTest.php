<?php

declare(strict_types=1);

namespace App\Tests\Application\Task\Transport\Controller\Api\V1;

use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TaskControllerSecurityTest extends WebTestCase
{
    private const string COUNT_ENDPOINT = self::API_URL_PREFIX . '/v1/tasks/count';

    /**
     * @throws Throwable
     */
    #[TestDox('`GET /api/v1/tasks/count` returns 401 for anonymous users.')]
    public function testCountReturns401ForAnonymousUser(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', self::COUNT_ENDPOINT);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('`GET /api/v1/tasks/count` returns 200 for logged users in me audience.')]
    public function testCountReturns200ForLoggedUser(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('GET', self::API_URL_PREFIX . '/v1/me/tasks/count');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('`GET /api/v1/tasks/count` returns 200 for admin users in admin audience.')]
    public function testCountReturns200ForAdminInAdminAudience(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('GET', self::API_URL_PREFIX . '/v1/admin/tasks/count');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
