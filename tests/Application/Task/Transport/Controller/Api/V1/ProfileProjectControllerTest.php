<?php

declare(strict_types=1);

namespace App\Tests\Application\Task\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileProjectControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/me/profile/projects';

    /**
     * @throws Throwable
     */
    public function testLoggedUserCanCreateAndListOwnProjects(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'name' => 'User Owned Project',
            'status' => 'active',
            'description' => 'Project created from me audience.',
        ]));

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $createdProject = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($createdProject);
        self::assertArrayHasKey('id', $createdProject);

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $projects = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($projects);

        $projectIds = array_column($projects, 'id');
        self::assertContains($createdProject['id'], $projectIds);
    }

    /**
     * @throws Throwable
     */
    public function testAnonymousUserCannotCreateProject(): void
    {
        $client = $this->getTestClient();

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'name' => 'Anonymous Project',
            'status' => 'active',
        ]));

        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}
