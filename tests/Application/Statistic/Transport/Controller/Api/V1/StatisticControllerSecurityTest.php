<?php

declare(strict_types=1);

namespace App\Tests\Application\Statistic\Transport\Controller\Api\V1;

use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class StatisticControllerSecurityTest extends WebTestCase
{
    private const string OVERVIEW_ENDPOINT = self::API_URL_PREFIX . '/v1/statistics/overview';

    /**
     * @throws Throwable
     */
    #[TestDox('`GET /api/v1/statistics/overview` returns 401 for anonymous users.')]
    public function testOverviewReturns401ForAnonymousUser(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', self::OVERVIEW_ENDPOINT);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('`GET /api/v1/statistics/overview` returns 403 for non-admin users.')]
    public function testOverviewReturns403ForLoggedNonAdminUser(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('GET', self::OVERVIEW_ENDPOINT);

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('`GET /api/v1/statistics/overview` returns 200 for admin users.')]
    public function testOverviewReturns200ForAdminUser(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('GET', self::OVERVIEW_ENDPOINT);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }
}
