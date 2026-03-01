<?php

declare(strict_types=1);

namespace App\Tests\Application\Company\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileCompanyControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/me/profile/companies';

    /**
     * @throws Throwable
     */
    public function testLoggedUserCanCreateAndListOwnCompanies(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'legalName' => 'Profile Company Created',
            'status' => 'active',
            'mainAddress' => '10 User Street, Paris',
        ]));

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $createdCompany = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($createdCompany);
        self::assertArrayHasKey('id', $createdCompany);
        self::assertSame('profile-company-created', (string)$createdCompany['slug']);

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $companies = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($companies);

        $companyIds = array_column($companies, 'id');
        self::assertContains($createdCompany['id'], $companyIds);
    }

    /**
     * @throws Throwable
     */
    public function testAnonymousUserCannotCreateCompany(): void
    {
        $client = $this->getTestClient();

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'legalName' => 'Anonymous Company',
            'status' => 'active',
            'mainAddress' => 'No Address',
        ]));

        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}
