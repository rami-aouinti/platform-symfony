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
    private const string COMPANY_PROJECTS_URL = self::API_URL_PREFIX . '/v1/me/profile/companies/%s/projects';

    /**
     * @throws Throwable
     */
    public function testCompaniesEndpointReturnsOwnedCompanyWithoutExplicitMembership(): void
    {
        $ownerClient = $this->getTestClient('dave-user', 'password-user');
        $ownerClient->request('POST', self::COMPANIES_URL, content: JSON::encode([
            'legalName' => 'Owned only in profile list',
            'status' => 'active',
            'mainAddress' => '10 Owner Street, Paris',
        ]));

        self::assertSame(Response::HTTP_CREATED, $ownerClient->getResponse()->getStatusCode());
        $createdCompany = JSON::decode((string)$ownerClient->getResponse()->getContent(), true);

        $ownerClient->request('GET', self::COMPANIES_URL);
        self::assertSame(Response::HTTP_OK, $ownerClient->getResponse()->getStatusCode());

        $companies = JSON::decode((string)$ownerClient->getResponse()->getContent(), true);
        self::assertContains($createdCompany['id'], array_column($companies, 'id'));
    }

    /**
     * @throws Throwable
     */
    public function testCompaniesEndpointReturnsActiveMembershipCompany(): void
    {
        $aliceClient = $this->getTestClient('alice-user', 'password-user');
        $aliceClient->request('GET', self::COMPANIES_URL);

        self::assertSame(Response::HTTP_OK, $aliceClient->getResponse()->getStatusCode());

        $aliceCompanies = JSON::decode((string)$aliceClient->getResponse()->getContent(), true);
        self::assertIsArray($aliceCompanies);
        self::assertEqualsCanonicalizing(
            [
                '30000000-0000-1000-8000-000000000001',
                '30000000-0000-1000-8000-000000000008',
            ],
            array_column($aliceCompanies, 'id'),
        );
    }

    /**
     * @throws Throwable
     */
    public function testCompaniesEndpointReturnsEmptyWhenUserHasNoOwnershipOrMembership(): void
    {
        $outsiderClient = $this->getTestClient('emma-user', 'password-user');
        $outsiderClient->request('GET', self::COMPANIES_URL);

        self::assertSame(Response::HTTP_OK, $outsiderClient->getResponse()->getStatusCode());

        $outsiderCompanies = JSON::decode((string)$outsiderClient->getResponse()->getContent(), true);
        self::assertIsArray($outsiderCompanies);
        self::assertSame([], $outsiderCompanies);
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

    /**
     * @throws Throwable
     */
    public function testCompanyProjectsEndpointReturnsProjectsForOwnedCompany(): void
    {
        $aliceClient = $this->getTestClient('alice-user', 'password-user');
        $aliceClient->request('GET', sprintf(self::COMPANY_PROJECTS_URL, '30000000-0000-1000-8000-000000000008'));

        self::assertSame(Response::HTTP_OK, $aliceClient->getResponse()->getStatusCode());

        $companyProjects = JSON::decode((string)$aliceClient->getResponse()->getContent(), true);
        self::assertIsArray($companyProjects);
        self::assertContains('70000000-0000-1000-8000-000000000008', array_column($companyProjects, 'id'));
    }

    /**
     * @throws Throwable
     */
    public function testCompanyProjectsEndpointReturnsProjectsForActiveMembershipCompany(): void
    {
        $aliceClient = $this->getTestClient('alice-user', 'password-user');
        $aliceClient->request('GET', sprintf(self::COMPANY_PROJECTS_URL, '30000000-0000-1000-8000-000000000001'));

        self::assertSame(Response::HTTP_OK, $aliceClient->getResponse()->getStatusCode());

        $companyProjects = JSON::decode((string)$aliceClient->getResponse()->getContent(), true);
        self::assertIsArray($companyProjects);

        $projectIds = array_column($companyProjects, 'id');
        self::assertContains('70000000-0000-1000-8000-000000000001', $projectIds);
        self::assertContains('70000000-0000-1000-8000-000000000006', $projectIds);
        self::assertContains('70000000-0000-1000-8000-000000000007', $projectIds);
    }

    /**
     * @throws Throwable
     */
    public function testCompanyProjectsEndpointReturnsForbiddenForInaccessibleCompany(): void
    {
        $emmaClient = $this->getTestClient('emma-user', 'password-user');
        $emmaClient->request('GET', sprintf(self::COMPANY_PROJECTS_URL, '30000000-0000-1000-8000-000000000001'));

        self::assertSame(Response::HTTP_FORBIDDEN, $emmaClient->getResponse()->getStatusCode());
    }
}
