<?php

declare(strict_types=1);

namespace App\Tests\Application\Company\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CompanyControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/companies';
    private const string COMPANY_ID = '30000000-0000-1000-8000-000000000001';

    /** @throws Throwable */
    public function testOwnerCanCreateShowEditAndDeleteCompany(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $payload = [
            'legalName' => 'Owner Created Company',
            'slug' => 'owner-created-company',
            'status' => 'active',
            'mainAddress' => '10 Owner Street, Paris',
        ];

        $client->request('POST', self::BASE_URL, content: JSON::encode($payload));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $created = JSON::decode((string) $client->getResponse()->getContent(), true);
        $companyId = (string) $created['id'];

        $client->request('GET', self::BASE_URL . '/' . $companyId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('PATCH', self::BASE_URL . '/' . $companyId, content: JSON::encode([
            'legalName' => 'Owner Company Edited',
        ]));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('DELETE', self::BASE_URL . '/' . $companyId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    #[DataProvider('unauthorizedUserProvider')]
    public function testNonOwnerAccessIsRefused(string $method): void
    {
        $client = $this->getTestClient('carol-user', 'password-user');

        $client->request($method, self::BASE_URL . '/' . self::COMPANY_ID, content: JSON::encode([
            'legalName' => 'Blocked update',
            'slug' => 'blocked-update',
            'status' => 'active',
        ]));

        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public static function unauthorizedUserProvider(): array
    {
        return [
            ['GET'],
            ['PUT'],
            ['PATCH'],
            ['DELETE'],
        ];
    }
}
