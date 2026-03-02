<?php

declare(strict_types=1);

namespace App\Tests\Application\Company\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CompanyMembersControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/companies';
    private const string COMPANY_ID = '30000000-0000-1000-8000-000000000001';
    private const string DAVE_USER_ID = '20000000-0000-1000-8000-000000000010';

    /**
     * @throws Throwable
     */
    public function testOwnerCanAttachMemberUsingIdFieldAsUserId(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('POST', self::BASE_URL . '/' . self::COMPANY_ID . '/members', content: JSON::encode([
            'id' => self::DAVE_USER_ID,
            'role' => 'member',
            'status' => 'invited',
        ]));

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $membership = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($membership);
        self::assertArrayHasKey('user', $membership);
        self::assertSame(self::DAVE_USER_ID, (string)$membership['user']['id']);
    }
}
