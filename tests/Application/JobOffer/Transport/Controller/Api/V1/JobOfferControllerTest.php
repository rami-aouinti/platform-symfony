<?php

declare(strict_types=1);

namespace App\Tests\Application\JobOffer\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JobOfferControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/job-offers';
    private const string COMPANY_ID = '30000000-0000-1000-8000-000000000001';
    private const string OFFER_ID = '40000000-0000-1000-8000-000000000001';

    /** @throws Throwable */
    public function testAuthorizedAuthorCanCreateEditAndDeleteOffer(): void
    {
        $authorClient = $this->getTestClient('john-user', 'password-user');

        $authorClient->request('POST', self::BASE_URL, content: JSON::encode([
            'title' => 'Offer authored by john-user',
            'description' => 'Creation flow by authorized author.',
            'location' => 'Paris',
            'employmentType' => 'full-time',
            'status' => 'open',
            'company' => self::COMPANY_ID,
        ]));

        self::assertSame(Response::HTTP_CREATED, $authorClient->getResponse()->getStatusCode());
        $created = JSON::decode((string) $authorClient->getResponse()->getContent(), true);
        $offerId = (string) $created['id'];

        $authorClient->request('PATCH', self::BASE_URL . '/' . $offerId, content: JSON::encode([
            'title' => 'Offer authored by john-user (edited)',
        ]));
        self::assertSame(Response::HTTP_OK, $authorClient->getResponse()->getStatusCode());

        $authorClient->request('DELETE', self::BASE_URL . '/' . $offerId);
        self::assertSame(Response::HTTP_OK, $authorClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testOfferVisibilityDependsOnUserRole(): void
    {
        $internalClient = $this->getTestClient('alice-user', 'password-user');
        $internalClient->request('GET', self::BASE_URL . '/' . self::OFFER_ID);
        self::assertSame(Response::HTTP_OK, $internalClient->getResponse()->getStatusCode());

        $externalClient = $this->getTestClient('carol-user', 'password-user');
        $externalClient->request('GET', self::BASE_URL . '/' . self::OFFER_ID);
        self::assertSame(Response::HTTP_FORBIDDEN, $externalClient->getResponse()->getStatusCode());
    }
}
