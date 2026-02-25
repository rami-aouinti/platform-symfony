<?php

declare(strict_types=1);

namespace App\Tests\Application\JobApplication\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class JobApplicationControllerTest extends WebTestCase
{
    private const string OFFER_ID = '40000000-0000-1000-8000-000000000001';
    private const string APPLICATION_ID = '50000000-0000-1000-8000-000000000001';

    /** @throws Throwable */
    public function testApplyDuplicateWithdrawDecisionAndOpenApiDocumentation(): void
    {
        $candidateClient = $this->getTestClient('carol-user', 'password-user');
        $candidateClient->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');
        self::assertSame(Response::HTTP_CONFLICT, $candidateClient->getResponse()->getStatusCode());

        $managerClient = $this->getTestClient('alice-user', 'password-user');
        $managerClient->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');
        self::assertSame(Response::HTTP_CREATED, $managerClient->getResponse()->getStatusCode());

        $created = JSON::decode((string) $managerClient->getResponse()->getContent(), true);
        $applicationId = (string) $created['id'];

        $candidateClient->request('PATCH', self::API_URL_PREFIX . '/v1/job-applications/' . self::APPLICATION_ID . '/withdraw');
        self::assertSame(Response::HTTP_OK, $candidateClient->getResponse()->getStatusCode());

        $authorClient = $this->getTestClient('john-user', 'password-user');
        $authorClient->request('PATCH', self::API_URL_PREFIX . '/v1/job-applications/' . $applicationId . '/accept');
        self::assertSame(Response::HTTP_OK, $authorClient->getResponse()->getStatusCode());

        $rejectionCandidate = $this->getTestClient('bob-admin', 'password-admin');
        $rejectionCandidate->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');
        self::assertSame(Response::HTTP_CREATED, $rejectionCandidate->getResponse()->getStatusCode());
        $toReject = JSON::decode((string) $rejectionCandidate->getResponse()->getContent(), true);

        $authorClient->request('PATCH', self::API_URL_PREFIX . '/v1/job-applications/' . $toReject['id'] . '/reject');
        self::assertSame(Response::HTTP_OK, $authorClient->getResponse()->getStatusCode());

        $managerClient->request('PATCH', self::API_URL_PREFIX . '/v1/job-applications/' . $applicationId . '/reject');
        self::assertSame(Response::HTTP_BAD_REQUEST, $managerClient->getResponse()->getStatusCode());

        $authorClient->request('PATCH', self::API_URL_PREFIX . '/v1/job-applications/' . self::APPLICATION_ID . '/withdraw');
        self::assertSame(Response::HTTP_FORBIDDEN, $authorClient->getResponse()->getStatusCode());

        $docsClient = $this->getTestClient();
        $docsClient->request('GET', '/api/doc.json');
        self::assertSame(Response::HTTP_OK, $docsClient->getResponse()->getStatusCode());
        $documentation = JSON::decode((string) $docsClient->getResponse()->getContent(), true);

        self::assertContains('Job Application Management', array_column($documentation['tags'] ?? [], 'name'));
        self::assertArrayHasKey('/api/v1/job-offers/{id}/apply', $documentation['paths']);
        self::assertArrayHasKey('/api/v1/job-applications/{id}/accept', $documentation['paths']);
        self::assertArrayHasKey('/api/v1/job-applications/{id}/reject', $documentation['paths']);
        self::assertArrayHasKey('/api/v1/job-applications/{id}/withdraw', $documentation['paths']);
    }
}
