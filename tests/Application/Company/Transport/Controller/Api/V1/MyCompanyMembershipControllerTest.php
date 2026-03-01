<?php

declare(strict_types=1);

namespace App\Tests\Application\Company\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MyCompanyMembershipControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/me/companies';
    private const string ACME_COMPANY_ID = '30000000-0000-1000-8000-000000000001';
    private const string EXTERNAL_COMPANY_ID = '30000000-0000-1000-8000-000000000005';
    private const string JOHN_USER_ID = '20000000-0000-1000-8000-000000000004';

    /**
     * @throws Throwable
     */
    public function testAuthenticatedUserCanListOwnMemberships(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $payload = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($payload);
        self::assertCount(2, $payload);

        foreach ($payload as $membership) {
            self::assertArrayHasKey('id', $membership);
            self::assertArrayHasKey('company', $membership);
            self::assertArrayHasKey('user', $membership);
            self::assertArrayHasKey('role', $membership);
            self::assertArrayHasKey('status', $membership);
            self::assertArrayHasKey('id', $membership['company']);
            self::assertArrayHasKey('id', $membership['user']);
            self::assertSame(self::JOHN_USER_ID, (string)$membership['user']['id']);
        }

        $companyIds = array_column(array_column($payload, 'company'), 'id');
        self::assertContains(self::ACME_COMPANY_ID, $companyIds);
    }

    /**
     * @throws Throwable
     */
    public function testAnonymousUserIsRejected(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    public function testAccessToUnrelatedCompanyMembershipIsDenied(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('GET', self::BASE_URL . '/' . self::EXTERNAL_COMPANY_ID . '/membership');
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
