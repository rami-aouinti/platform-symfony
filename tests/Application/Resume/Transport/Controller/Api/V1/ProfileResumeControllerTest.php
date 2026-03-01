<?php

declare(strict_types=1);

namespace App\Tests\Application\Resume\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileResumeControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/me/profile/resumes';

    /**
     * @throws Throwable
     */
    public function testLoggedUserCanCreateAndListOwnResumes(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'title' => 'John Resume',
            'summary' => 'Resume summary that is long enough.',
            'isPublic' => false,
        ]));

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $created = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($created);
        self::assertArrayHasKey('id', $created);

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $list = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($list);

        self::assertContains($created['id'], array_column($list, 'id'));
    }
}
