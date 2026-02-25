<?php

declare(strict_types=1);

namespace App\Tests\Application\JobOffer\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function array_column;

class JobOfferControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/job-offers';
    private const string COMPANY_ID = '30000000-0000-1000-8000-000000000001';
    private const string OFFER_ID = '60000000-0000-1000-8000-000000000001';

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

    /** @throws Throwable */
    public function testMyRouteReturnsOwnedAndManageableOffers(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');
        $client->request('GET', self::BASE_URL . '/my');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerIds = array_column($offers, 'id');

        self::assertContains('60000000-0000-1000-8000-000000000001', $offerIds);
        self::assertContains('60000000-0000-1000-8000-000000000002', $offerIds);
        self::assertContains('60000000-0000-1000-8000-000000000004', $offerIds);
        self::assertNotContains('60000000-0000-1000-8000-000000000003', $offerIds);
    }

    /** @throws Throwable */
    public function testAvailableRouteReturnsOnlyOpenOffersUserCanApplyTo(): void
    {
        $client = $this->getTestClient('carol-user', 'password-user');
        $client->request('GET', self::BASE_URL . '/available');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerIds = array_column($offers, 'id');

        self::assertContains('60000000-0000-1000-8000-000000000001', $offerIds);
        self::assertContains('60000000-0000-1000-8000-000000000002', $offerIds);
        self::assertNotContains('60000000-0000-1000-8000-000000000003', $offerIds);
        self::assertNotContains('60000000-0000-1000-8000-000000000004', $offerIds);
    }

    /** @throws Throwable */
    public function testMyRoutePaginationIsAppliedAfterVisibilityConstraints(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');
        $client->request('GET', self::BASE_URL . '/my', [
            'order' => ['title' => 'ASC'],
            'limit' => 2,
            'offset' => 1,
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerIds = array_column($offers, 'id');

        self::assertCount(2, $offers);
        self::assertSame([
            '60000000-0000-1000-8000-000000000002',
            '60000000-0000-1000-8000-000000000001',
        ], $offerIds);
    }

    /** @throws Throwable */
    public function testAvailableRoutePaginationIsAppliedAfterVisibilityConstraints(): void
    {
        $client = $this->getTestClient('carol-user', 'password-user');
        $client->request('GET', self::BASE_URL . '/available', [
            'order' => ['title' => 'ASC'],
            'limit' => 1,
            'offset' => 1,
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerIds = array_column($offers, 'id');

        self::assertCount(1, $offers);
        self::assertSame([
            '60000000-0000-1000-8000-000000000001',
        ], $offerIds);
    }

    /** @throws Throwable */
    public function testFindActionSupportsCombinedBusinessFilters(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');
        $client->request('GET', self::BASE_URL, [
            'remotePolicy' => 'hybrid',
            'employmentType' => 'full-time',
            'workTime' => 'full-time',
            'city' => '64000000-0000-1000-8000-000000000001',
            'region' => '63000000-0000-1000-8000-000000000001',
            'jobCategory' => '65000000-0000-1000-8000-000000000001',
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        self::assertCount(1, $offers);
        self::assertSame('60000000-0000-1000-8000-000000000001', $offers[0]['id']);
    }

    /** @throws Throwable */
    public function testFindActionSupportsMultiValueFilters(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');
        $client->request('GET', self::BASE_URL, [
            'remotePolicy' => ['hybrid', 'remote'],
            'skills' => [
                '61000000-0000-1000-8000-000000000001',
                '61000000-0000-1000-8000-000000000003',
            ],
            'languages' => [
                '62000000-0000-1000-8000-000000000003',
            ],
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerIds = array_column($offers, 'id');

        self::assertContains('60000000-0000-1000-8000-000000000002', $offerIds);
        self::assertNotContains('60000000-0000-1000-8000-000000000001', $offerIds);
    }

    /** @throws Throwable */
    public function testFindActionSupportsSalaryBoundsFilters(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');
        $client->request('GET', self::BASE_URL, [
            'salaryMin' => '68000',
            'salaryMax' => '90000',
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $offers = JSON::decode((string) $client->getResponse()->getContent(), true);
        $offerIds = array_column($offers, 'id');

        self::assertContains('60000000-0000-1000-8000-000000000001', $offerIds);
        self::assertContains('60000000-0000-1000-8000-000000000002', $offerIds);
        self::assertNotContains('60000000-0000-1000-8000-000000000003', $offerIds);
    }

    /** @throws Throwable */
    public function testFacetsActionReturnsCountsCoherentWithCurrentFilters(): void
    {
        $client = $this->getTestClient('alice-user', 'password-user');
        $client->request('GET', self::BASE_URL . '/facets', [
            'remotePolicy' => ['hybrid', 'remote'],
            'employmentType' => 'full-time',
        ]);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $payload = JSON::decode((string) $client->getResponse()->getContent(), true);

        $facetsByKey = [];
        foreach ($payload['facets'] as $facetDefinition) {
            self::assertSame('count_desc,label_asc', $facetDefinition['sort']);
            $facetsByKey[$facetDefinition['key']] = $facetDefinition['values'];
        }

        self::assertArrayHasKey('skills', $facetsByKey);
        self::assertArrayHasKey('languages', $facetsByKey);
        self::assertArrayHasKey('jobCategories', $facetsByKey);

        $skillCounts = [];
        foreach ($facetsByKey['skills'] as $facet) {
            $skillCounts[$facet['id']] = $facet['count'];
        }

        $languageCounts = [];
        foreach ($facetsByKey['languages'] as $facet) {
            $languageCounts[$facet['id']] = $facet['count'];
        }

        $jobCategoryCounts = [];
        foreach ($facetsByKey['jobCategories'] as $facet) {
            $jobCategoryCounts[$facet['id']] = $facet['count'];
        }

        self::assertSame(1, $skillCounts['61000000-0000-1000-8000-000000000001'] ?? null);
        self::assertSame(1, $skillCounts['61000000-0000-1000-8000-000000000003'] ?? null);
        self::assertSame(2, $languageCounts['62000000-0000-1000-8000-000000000002'] ?? null);
        self::assertSame(1, $languageCounts['62000000-0000-1000-8000-000000000003'] ?? null);
        self::assertSame(1, $jobCategoryCounts['65000000-0000-1000-8000-000000000001'] ?? null);
        self::assertSame(1, $jobCategoryCounts['65000000-0000-1000-8000-000000000002'] ?? null);
    }
}
