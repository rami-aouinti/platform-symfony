<?php

declare(strict_types=1);

namespace App\Tests\Application\Configuration\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ConfigurationControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/configuration';

    /**
     * @throws Throwable
     */
    #[DataProvider('authenticatedUserProvider')]
    public function testAuthenticatedUserCanCreateListFindUpdatePatchAndDeleteConfiguration(
        string $username,
        string $password,
    ): void {
        $client = $this->getTestClient($username, $password);

        $payload = [
            'code' => 'cfg-' . uniqid(),
            'keyName' => 'feature.toggle.' . uniqid(),
            'value' => 'enabled',
            'status' => 'active',
        ];

        $client->request('POST', self::BASE_URL, content: JSON::encode($payload));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $created = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($created);
        self::assertArrayHasKey('id', $created);
        self::assertSame($payload['code'], $created['code']);
        self::assertSame($payload['keyName'], $created['keyName']);
        $configurationId = (string)$created['id'];

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $list = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($list);
        self::assertNotEmpty($list);

        $client->request('GET', self::BASE_URL . '/' . $configurationId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $found = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame($configurationId, $found['id']);
        self::assertSame($payload['code'], $found['code']);

        $updatePayload = [
            'code' => 'cfg-updated-' . uniqid(),
            'keyName' => 'feature.toggle.updated.' . uniqid(),
            'value' => 'disabled',
            'status' => 'inactive',
        ];

        $client->request('PUT', self::BASE_URL . '/' . $configurationId, content: JSON::encode($updatePayload));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $updated = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame($updatePayload['code'], $updated['code']);
        self::assertSame($updatePayload['keyName'], $updated['keyName']);
        self::assertSame($updatePayload['value'], $updated['value']);
        self::assertSame($updatePayload['status'], $updated['status']);

        $patchPayload = [
            'value' => 'partially-updated',
        ];

        $client->request('PATCH', self::BASE_URL . '/' . $configurationId, content: JSON::encode($patchPayload));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $patched = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame($patchPayload['value'], $patched['value']);
        self::assertSame($updatePayload['code'], $patched['code']);

        $client->request('DELETE', self::BASE_URL . '/' . $configurationId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('configurationActionsProvider')]
    public function testUnauthenticatedUserCannotAccessConfigurationEndpoints(string $method, string $uri): void
    {
        $client = $this->getTestClient();

        $client->request($method, $uri);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    public function testCreateReturnsValidationErrorsForInvalidPayload(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('POST', self::BASE_URL, content: JSON::encode([
            'code' => 'a',
            'keyName' => '',
            'status' => 'a',
        ]));

        self::assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $responseData = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertArrayHasKey('message', $responseData);
    }

    /**
     * @throws Throwable
     */
    public function testUpdateReturnsValidationErrorsForMissingRequiredFields(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $createPayload = [
            'code' => 'cfg-to-update-' . uniqid(),
            'keyName' => 'required.fields.' . uniqid(),
            'value' => 'initial',
            'status' => 'active',
        ];

        $client->request('POST', self::BASE_URL, content: JSON::encode($createPayload));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $created = JSON::decode((string)$client->getResponse()->getContent(), true);
        $configurationId = (string)$created['id'];

        $client->request('PUT', self::BASE_URL . '/' . $configurationId, content: JSON::encode([
            'keyName' => '',
            'status' => 'a',
        ]));

        self::assertSame(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
        $responseData = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertArrayHasKey('message', $responseData);

        $client->request('DELETE', self::BASE_URL . '/' . $configurationId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function authenticatedUserProvider(): Generator
    {
        yield ['john-user', 'password-user'];
        yield ['john-admin', 'password-admin'];
    }

    /**
     * @return Generator<array{0: string, 1: string}>
     */
    public static function configurationActionsProvider(): Generator
    {
        yield ['GET', self::BASE_URL];
        yield ['POST', self::BASE_URL];
        yield ['GET', self::BASE_URL . '/00000000-0000-0000-0000-000000000000'];
        yield ['PUT', self::BASE_URL . '/00000000-0000-0000-0000-000000000000'];
        yield ['PATCH', self::BASE_URL . '/00000000-0000-0000-0000-000000000000'];
        yield ['DELETE', self::BASE_URL . '/00000000-0000-0000-0000-000000000000'];
    }
}
