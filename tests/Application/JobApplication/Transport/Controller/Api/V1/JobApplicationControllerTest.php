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
    private const string UNAUTHORIZED_APPLICATION_ID = '50000000-0000-1000-8000-000000000004';
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/job-applications';

    /** @throws Throwable */
    public function testCandidateCanApplyToOpenOffer(): void
    {
        $candidateClient = $this->getTestClient('bob-admin', 'password-admin');
        $candidateClient->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');

        self::assertSame(Response::HTTP_CREATED, $candidateClient->getResponse()->getStatusCode());
        $payload = JSON::decode((string) $candidateClient->getResponse()->getContent(), true);
        self::assertSame('pending', $payload['status']);
    }

    /** @throws Throwable */
    public function testDuplicateApplicationIsBlocked(): void
    {
        $candidateClient = $this->getTestClient('carol-user', 'password-user');
        $candidateClient->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');

        self::assertSame(Response::HTTP_CONFLICT, $candidateClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testAcceptAndRejectAreAllowedOnlyForOfferOwnerOrAuthorizedManager(): void
    {
        $candidateClient = $this->getTestClient('alice-user', 'password-user');
        $candidateClient->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');
        self::assertSame(Response::HTTP_CREATED, $candidateClient->getResponse()->getStatusCode());

        $application = JSON::decode((string) $candidateClient->getResponse()->getContent(), true);
        $applicationId = (string) $application['id'];

        $forbiddenClient = $this->getTestClient('carol-user', 'password-user');
        $forbiddenClient->request('PATCH', self::BASE_URL . '/' . $applicationId . '/accept');
        self::assertSame(Response::HTTP_FORBIDDEN, $forbiddenClient->getResponse()->getStatusCode());

        $ownerClient = $this->getTestClient('john-user', 'password-user');
        $ownerClient->request('PATCH', self::BASE_URL . '/' . $applicationId . '/accept');
        self::assertSame(Response::HTTP_OK, $ownerClient->getResponse()->getStatusCode());

        $managerClient = $this->getTestClient('alice-user', 'password-user');
        $managerClient->request('PATCH', self::BASE_URL . '/' . $applicationId . '/reject');
        self::assertSame(Response::HTTP_BAD_REQUEST, $managerClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testWithdrawIsAllowedOnlyForCandidate(): void
    {
        $candidateClient = $this->getTestClient('carol-user', 'password-user');
        $candidateClient->request('PATCH', self::BASE_URL . '/' . self::APPLICATION_ID . '/withdraw');
        self::assertSame(Response::HTTP_OK, $candidateClient->getResponse()->getStatusCode());

        $authorClient = $this->getTestClient('john-user', 'password-user');
        $authorClient->request('PATCH', self::BASE_URL . '/' . self::APPLICATION_ID . '/withdraw');
        self::assertSame(Response::HTTP_FORBIDDEN, $authorClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testInvalidTransitionsAreRejected(): void
    {
        $candidateClient = $this->getTestClient('bob-admin', 'password-admin');
        $candidateClient->request('POST', self::API_URL_PREFIX . '/v1/job-offers/' . self::OFFER_ID . '/apply');
        self::assertSame(Response::HTTP_CREATED, $candidateClient->getResponse()->getStatusCode());
        $application = JSON::decode((string) $candidateClient->getResponse()->getContent(), true);
        $applicationId = (string) $application['id'];

        $ownerClient = $this->getTestClient('john-user', 'password-user');
        $ownerClient->request('PATCH', self::BASE_URL . '/' . $applicationId . '/reject');
        self::assertSame(Response::HTTP_OK, $ownerClient->getResponse()->getStatusCode());

        $ownerClient->request('PATCH', self::BASE_URL . '/' . $applicationId . '/accept');
        self::assertSame(Response::HTTP_BAD_REQUEST, $ownerClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testApplicationVisibilityListDependsOnRole(): void
    {
        $candidateClient = $this->getTestClient('carol-user', 'password-user');
        $candidateClient->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $candidateClient->getResponse()->getStatusCode());
        $candidateList = JSON::decode((string) $candidateClient->getResponse()->getContent(), true);
        self::assertIsArray($candidateList);
        self::assertNotEmpty($candidateList);

        foreach ($candidateList as $application) {
            self::assertNotSame(self::UNAUTHORIZED_APPLICATION_ID, $application['id'] ?? null);
        }

        $candidateClient->request('GET', self::BASE_URL . '/' . self::APPLICATION_ID);
        self::assertSame(Response::HTTP_OK, $candidateClient->getResponse()->getStatusCode());

        $candidateClient->request('GET', self::BASE_URL . '/' . self::UNAUTHORIZED_APPLICATION_ID);
        self::assertSame(Response::HTTP_NOT_FOUND, $candidateClient->getResponse()->getStatusCode());

        $outsiderClient = $this->getTestClient('bob-admin', 'password-admin');
        $outsiderClient->request('GET', self::BASE_URL . '/' . self::APPLICATION_ID);
        self::assertSame(Response::HTTP_NOT_FOUND, $outsiderClient->getResponse()->getStatusCode());
    }
}
