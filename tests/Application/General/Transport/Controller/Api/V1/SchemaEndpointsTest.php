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
        self::assertArrayHasKey('relations', $data);
    }

    /**
     * @throws Throwable
     */
    public function testTaskSchemaContainsKnownRelationAndEditableField(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');
        $client->request('GET', self::API_URL_PREFIX . '/v1/tasks/schema');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $data = JSON::decode((string)$client->getResponse()->getContent(), true);

        self::assertSame('object', $data['relations']['project']['type'] ?? null);
        self::assertContains('project', $data['relations']['project']['groups'] ?? []);
        self::assertContains('title', $data['editable']);
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
