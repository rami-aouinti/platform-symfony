<?php

declare(strict_types=1);

namespace App\Tests\Application\ApplicationCatalog\Transport\Controller\Api\V1;

use App\ApplicationCatalog\Domain\Repository\Interfaces\ApplicationRepositoryInterface;
use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use JsonException;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class ProfileApplicationControllerTest extends WebTestCase
{
    private const string PROFILE_URL = self::API_URL_PREFIX . '/v1/profile/applications';
    private const string CATALOG_URL = self::API_URL_PREFIX . '/v1/applications';

    /**
     * @throws Throwable
     */
    #[TestDox('GET /api/v1/applications returns catalog for anonymous user')]
    public function testCatalogIsPublic(): void
    {
        $client = $this->getTestClient();
        $client->request(Request::METHOD_GET, self::CATALOG_URL);

        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);

        $payload = $this->decodeResponse($response->getContent());
        self::assertArrayHasKey('items', $payload);
        self::assertIsArray($payload['items']);
        self::assertNotEmpty($payload['items']);
        self::assertArrayHasKey('description', $payload['items'][0]);
        self::assertNotEmpty($payload['items'][0]['description']);
        self::assertArrayHasKey('enabled', $payload['items'][0]);
        self::assertNull($payload['items'][0]['enabled']);
        self::assertArrayHasKey('userApplicationId', $payload['items'][0]);
        self::assertNull($payload['items'][0]['userApplicationId']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('GET /api/v1/profile/applications requires authentication')]
    public function testProfileListRequiresAuthentication(): void
    {
        $client = $this->getTestClient();
        $client->request(Request::METHOD_GET, self::PROFILE_URL);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('PATCH /api/v1/profile/applications/{id} toggles only current user activation')]
    public function testPatchTogglesOnlyCurrentUser(): void
    {
        $applicationRepository = static::getContainer()->get(ApplicationRepositoryInterface::class);
        $application = $applicationRepository->findOneByName('CRM');
        self::assertNotNull($application);

        $johnClient = $this->getTestClient('john-user', 'password-user');
        $johnClient->request(
            Request::METHOD_PATCH,
            self::PROFILE_URL . '/' . $application->getId(),
            content: JSON::encode(['active' => true]),
        );

        self::assertSame(Response::HTTP_OK, $johnClient->getResponse()->getStatusCode(), "Response:\n" . $johnClient->getResponse());

        $johnClient->request(Request::METHOD_GET, self::PROFILE_URL);
        $johnList = $this->decodeResponse($johnClient->getResponse()->getContent());
        self::assertTrue($this->findEnabledStateByName($johnList['items'], 'CRM'));

        $aliceClient = $this->getTestClient('alice-user', 'password-user');
        $aliceClient->request(Request::METHOD_GET, self::PROFILE_URL);
        $aliceList = $this->decodeResponse($aliceClient->getResponse()->getContent());
        self::assertFalse($this->findEnabledStateByName($aliceList['items'], 'CRM'));
    }



    /**
     * @throws Throwable
     */
    #[TestDox('POST /api/v1/profile/applications/{id}/activate and /deactivate toggle current user activation')]
    public function testActivateAndDeactivateEndpoints(): void
    {
        $applicationRepository = static::getContainer()->get(ApplicationRepositoryInterface::class);
        $application = $applicationRepository->findOneByName('School');
        self::assertNotNull($application);

        $client = $this->getTestClient('john-user', 'password-user');

        $client->request(Request::METHOD_POST, self::PROFILE_URL . '/' . $application->getId() . '/activate');
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode(), "Response:
" . $client->getResponse());

        $client->request(Request::METHOD_GET, self::PROFILE_URL);
        $list = $this->decodeResponse($client->getResponse()->getContent());
        self::assertTrue($this->findEnabledStateByName($list['items'], 'School'));

        $client->request(Request::METHOD_POST, self::PROFILE_URL . '/' . $application->getId() . '/deactivate');
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode(), "Response:
" . $client->getResponse());

        $client->request(Request::METHOD_GET, self::PROFILE_URL);
        $list = $this->decodeResponse($client->getResponse()->getContent());
        self::assertFalse($this->findEnabledStateByName($list['items'], 'School'));
    }

    /**
     * @throws Throwable
     */
    #[TestDox('POST /api/v1/profile/applications/{id}/attach and DELETE /detach attach and detach current user application')]
    public function testAttachAndDetachEndpoints(): void
    {
        $applicationRepository = static::getContainer()->get(ApplicationRepositoryInterface::class);
        $application = $applicationRepository->findOneByName('ERP');
        self::assertNotNull($application);

        $client = $this->getTestClient('john-user', 'password-user');

        $client->request(Request::METHOD_POST, self::PROFILE_URL . '/' . $application->getId() . '/attach');
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode(), "Response:
" . $client->getResponse());

        $attachPayload = $this->decodeResponse($client->getResponse()->getContent());
        self::assertArrayHasKey('id', $attachPayload);

        $client->request(Request::METHOD_GET, self::PROFILE_URL);
        $list = $this->decodeResponse($client->getResponse()->getContent());
        self::assertTrue($this->findEnabledStateByName($list['items'], 'ERP'));
        self::assertSame($attachPayload['id'], $this->findUserApplicationIdByName($list['items'], 'ERP'));

        $client->request(Request::METHOD_DELETE, self::PROFILE_URL . '/' . $application->getId() . '/detach');
        self::assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode(), "Response:
" . $client->getResponse());

        $client->request(Request::METHOD_GET, self::PROFILE_URL);
        $list = $this->decodeResponse($client->getResponse()->getContent());
        self::assertFalse($this->findEnabledStateByName($list['items'], 'ERP'));
    }



    /**
     * @throws Throwable
     */
    #[TestDox('POST /api/v1/profile/user-applications creates a user application with generated key')]
    public function testCreateUserApplicationEndpoint(): void
    {
        $applicationRepository = static::getContainer()->get(ApplicationRepositoryInterface::class);
        $application = $applicationRepository->findOneByName('CRM');
        self::assertNotNull($application);

        $client = $this->getTestClient('john-user', 'password-user');
        $client->request(
            Request::METHOD_POST,
            self::API_URL_PREFIX . '/v1/profile/user-applications',
            content: JSON::encode([
                'applicationId' => $application->getId(),
                'name' => 'CRM Special',
                'logo' => 'https://example.test/crm-special.png',
                'description' => 'Custom instance',
                'public' => true,
            ]),
        );

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode(), "Response:
" . $client->getResponse());

        $payload = $this->decodeResponse($client->getResponse()->getContent());
        self::assertSame('CRM Special', $payload['name'] ?? null);
        self::assertSame('crm-special', $payload['keyName'] ?? null);
        self::assertTrue((bool)($payload['public'] ?? false));
        self::assertTrue((bool)($payload['owner'] ?? false));
    }

    /**
     * @param string|false $content
     *
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function decodeResponse(string|false $content): array
    {
        self::assertNotFalse($content);

        /** @var array<string, mixed> $decoded */
        $decoded = JSON::decode($content, true);

        return $decoded;
    }


    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function findUserApplicationIdByName(array $items, string $name): ?string
    {
        foreach ($items as $item) {
            if (($item['name'] ?? null) === $name) {
                $userApplicationId = $item['userApplicationId'] ?? null;

                return is_string($userApplicationId) ? $userApplicationId : null;
            }
        }

        self::fail('Application was not found in payload.');
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    private function findEnabledStateByName(array $items, string $name): bool
    {
        foreach ($items as $item) {
            if (($item['name'] ?? null) === $name) {
                return (bool)($item['enabled'] ?? false);
            }
        }

        self::fail('Application was not found in payload.');
    }
}