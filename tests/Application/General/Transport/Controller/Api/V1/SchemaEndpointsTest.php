<?php

declare(strict_types=1);

namespace App\Tests\Application\General\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SchemaEndpointsTest extends WebTestCase
{
    /**
     * @throws Throwable
     */
    #[DataProvider('schemaEndpointProvider')]
    public function testSchemaEndpointReturnsExpectedMetadata(
        string $url,
        string $username,
        string $password
    ): void {
        $client = $this->getTestClient($username, $password);
        $client->request('GET', self::API_URL_PREFIX . $url . '/schema');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode(), $url);

        $data = JSON::decode((string)$client->getResponse()->getContent(), true);

        self::assertArrayHasKey('displayable', $data);
        self::assertArrayHasKey('editable', $data);
        self::assertIsArray($data['displayable']);
        self::assertIsArray($data['editable']);
    }

    /**
     * @throws Throwable
     */
    public function testTaskSchemaContainsTypedFieldMetadata(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');
        $client->request('GET', self::API_URL_PREFIX . '/v1/tasks/schema');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $data = JSON::decode((string)$client->getResponse()->getContent(), true);

        $displayableByName = [];

        foreach ($data['displayable'] as $field) {
            $displayableByName[$field['name']] = $field;
        }

        $editableByName = [];

        foreach ($data['editable'] as $field) {
            $editableByName[$field['name']] = $field;
        }

        self::assertSame('normal', $displayableByName['title']['type'] ?? null);
        self::assertSame('object', $displayableByName['project']['type'] ?? null);
        self::assertSame('/api/v1/projects', $displayableByName['project']['endpoint'] ?? null);
        self::assertSame('object', $editableByName['project']['type'] ?? null);
    }


    /**
     * @throws Throwable
     */
    public function testRoleSchemaSupportsManualConfiguration(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');
        $client->request('GET', self::API_URL_PREFIX . '/v1/role/schema');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $data = JSON::decode((string)$client->getResponse()->getContent(), true);

        self::assertSame('id', $data['displayable'][0]['name'] ?? null);
        self::assertSame('description', $data['editable'][0]['name'] ?? null);
        self::assertSame('object', $data['displayable'][2]['type'] ?? null);
        self::assertSame('/api/v1/user-groups', $data['displayable'][2]['endpoint'] ?? null);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function schemaEndpointProvider(): array
    {
        return [
            'api-key-v1' => ['/v1/api_key', 'john-admin', 'password-admin'],
            'blog-tag' => ['/v1/blog-tags', 'john-user', 'password-user'],
            'company' => ['/v1/companies', 'john-user', 'password-user'],
            'configuration' => ['/v1/configuration', 'john-user', 'password-user'],
            'quiz' => ['/v1/quizzes', 'john-user', 'password-user'],
            'offer' => ['/v1/offers', 'john-user', 'password-user'],
            'role' => ['/v1/role', 'john-admin', 'password-admin'],
            'task' => ['/v1/tasks', 'john-user', 'password-user'],
            'user' => ['/v1/user', 'john-admin', 'password-admin'],
        ];
    }
}
