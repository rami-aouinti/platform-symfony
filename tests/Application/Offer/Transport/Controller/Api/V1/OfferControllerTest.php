<?php

declare(strict_types=1);

namespace App\Tests\Application\Offer\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OfferControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/offers';
    private const string COMPANY_ID = '30000000-0000-1000-8000-000000000001';
    private const string OFFER_ID = '40000000-0000-1000-8000-000000000001';

    /** @throws Throwable */
    public function testAuthorCanCreateShowEditAndDeleteOffer(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $payload = [
            'title' => 'New Author Offer',
            'description' => 'Created by author',
            'status' => 'draft',
            'company' => self::COMPANY_ID,
        ];

        $client->request('POST', self::BASE_URL, content: JSON::encode($payload));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $created = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerId = (string) $created['id'];

        $client->request('GET', self::BASE_URL . '/' . $offerId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('PATCH', self::BASE_URL . '/' . $offerId, content: JSON::encode([
            'title' => 'New Author Offer Edited',
        ]));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('DELETE', self::BASE_URL . '/' . $offerId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testManagerCanCreateShowEditAndDeleteOffer(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');

        $payload = [
            'title' => 'Manager Offer',
            'description' => 'Created by manager',
            'status' => 'draft',
            'company' => self::COMPANY_ID,
        ];

        $client->request('POST', self::BASE_URL, content: JSON::encode($payload));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $created = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerId = (string) $created['id'];

        $client->request('GET', self::BASE_URL . '/' . self::OFFER_ID);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('PATCH', self::BASE_URL . '/' . $offerId, content: JSON::encode([
            'title' => 'Manager Offer Edited',
        ]));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('DELETE', self::BASE_URL . '/' . $offerId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testExternalUserCannotManageOffers(): void
    {
        $client = $this->getTestClient('carol-user', 'password-user');

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'title' => 'Blocked External Offer',
            'description' => 'Should fail',
            'status' => 'draft',
            'company' => self::COMPANY_ID,
        ]));
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        $client->request('GET', self::BASE_URL . '/' . self::OFFER_ID);
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        $client->request('PATCH', self::BASE_URL . '/' . self::OFFER_ID, content: JSON::encode([
            'title' => 'Blocked edit',
        ]));
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        $client->request('DELETE', self::BASE_URL . '/' . self::OFFER_ID);
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
