<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CompaniesControllerTest extends WebTestCase
{
    private const string COMPANIES_URL = self::API_URL_PREFIX . '/v1/me/profile/companies';
    private const string PROJECTS_URL = self::API_URL_PREFIX . '/v1/me/profile/projects';

    /**
     * @throws Throwable
     */
    public function testCompaniesEndpointReturnsOnlyCurrentUserCompanies(): void
    {
        $johnClient = $this->getTestClient('john-user', 'password-user');
        $johnClient->request('GET', self::COMPANIES_URL);

        self::assertSame(Response::HTTP_OK, $johnClient->getResponse()->getStatusCode());

        $johnCompanies = JSON::decode((string)$johnClient->getResponse()->getContent(), true);
        self::assertIsArray($johnCompanies);
        self::assertCount(2, $johnCompanies);

        $johnCompanyIds = array_column($johnCompanies, 'id');
        self::assertEqualsCanonicalizing(
            [
                '30000000-0000-1000-8000-000000000001',
                '30000000-0000-1000-8000-000000000008',
            ],
            $johnCompanyIds,
        );

        foreach ($johnCompanies as $company) {
            self::assertArrayHasKey('id', $company);
            self::assertArrayHasKey('legalName', $company);
            self::assertArrayHasKey('status', $company);
            self::assertArrayHasKey('photoUrl', $company);
        }

        $carolClient = $this->getTestClient('carol-user', 'password-user');
        $carolClient->request('GET', self::COMPANIES_URL);

        self::assertSame(Response::HTTP_OK, $carolClient->getResponse()->getStatusCode());

        $carolCompanies = JSON::decode((string)$carolClient->getResponse()->getContent(), true);
        self::assertCount(1, $carolCompanies);
        self::assertSame('30000000-0000-1000-8000-000000000005', (string)$carolCompanies[0]['id']);
    }

    /**
     * @throws Throwable
     */
    public function testProjectsEndpointContainsDirectOwnedProjects(): void
    {
        $johnClient = $this->getTestClient('john-user', 'password-user');
        $johnClient->request('GET', self::PROJECTS_URL);

        self::assertSame(Response::HTTP_OK, $johnClient->getResponse()->getStatusCode());

        $johnProjects = JSON::decode((string)$johnClient->getResponse()->getContent(), true);
        self::assertIsArray($johnProjects);

        $johnProjectIds = array_column($johnProjects, 'id');
        self::assertContains('70000000-0000-1000-8000-000000000001', $johnProjectIds);

        foreach ($johnProjects as $project) {
            self::assertArrayHasKey('id', $project);
            self::assertArrayHasKey('name', $project);
            self::assertArrayHasKey('description', $project);
            self::assertArrayHasKey('status', $project);
            self::assertArrayHasKey('photoUrl', $project);
        }
    }

    /**
     * @throws Throwable
     */
    public function testProjectsEndpointContainsCompanyOwnerProjects(): void
    {
        $aliceClient = $this->getTestClient('alice-user', 'password-user');
        $aliceClient->request('GET', self::PROJECTS_URL);

        self::assertSame(Response::HTTP_OK, $aliceClient->getResponse()->getStatusCode());

        $aliceProjects = JSON::decode((string)$aliceClient->getResponse()->getContent(), true);
        self::assertIsArray($aliceProjects);

        $aliceProjectIds = array_column($aliceProjects, 'id');
        self::assertContains('70000000-0000-1000-8000-000000000002', $aliceProjectIds);
        self::assertContains('70000000-0000-1000-8000-000000000003', $aliceProjectIds);
        self::assertContains('70000000-0000-1000-8000-000000000008', $aliceProjectIds);
    }

    /**
     * @throws Throwable
     */
    public function testProjectsEndpointContainsActiveMembershipCompanyProjects(): void
    {
        $aliceClient = $this->getTestClient('alice-user', 'password-user');
        $aliceClient->request('GET', self::PROJECTS_URL);

        self::assertSame(Response::HTTP_OK, $aliceClient->getResponse()->getStatusCode());

        $aliceProjects = JSON::decode((string)$aliceClient->getResponse()->getContent(), true);
        self::assertIsArray($aliceProjects);

        $aliceProjectIds = array_column($aliceProjects, 'id');
        self::assertContains('70000000-0000-1000-8000-000000000001', $aliceProjectIds);
        self::assertContains('70000000-0000-1000-8000-000000000006', $aliceProjectIds);
        self::assertContains('70000000-0000-1000-8000-000000000007', $aliceProjectIds);
    }
}
