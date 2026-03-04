<?php

declare(strict_types=1);

namespace App\Tests\Application\ApplicationCatalog\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class AdminApplicationControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/admin/applications';

    /**
     * @throws Throwable
     */
    public function testAdminCanCreateListFindUpdatePatchAndDeleteApplication(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $createPayload = [
            'keyName' => 'supportdesk-' . uniqid(),
            'name' => 'SupportDesk-' . uniqid(),
            'logo' => 'https://cdn.fake.example/apps/supportdesk-logo.png',
            'description' => 'Gestion SAV et tickets clients.',
            'active' => true,
        ];

        $client->request('POST', self::BASE_URL, content: JSON::encode($createPayload));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $created = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame($createPayload['description'], $created['description']);
        $applicationId = (string)$created['id'];

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request('GET', self::BASE_URL . '/' . $applicationId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $updatePayload = [
            'keyName' => 'supportdesk-updated-' . uniqid(),
            'name' => 'SupportDeskUpdated-' . uniqid(),
            'logo' => 'https://cdn.fake.example/apps/supportdesk-logo-updated.png',
            'description' => 'Gestion SAV avancée.',
            'active' => false,
        ];

        $client->request('PUT', self::BASE_URL . '/' . $applicationId, content: JSON::encode($updatePayload));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $updated = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame($updatePayload['description'], $updated['description']);
        self::assertFalse((bool)$updated['active']);

        $client->request('PATCH', self::BASE_URL . '/' . $applicationId, content: JSON::encode([
            'description' => 'Description patchée.',
        ]));
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $patched = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame('Description patchée.', $patched['description']);

        $client->request('DELETE', self::BASE_URL . '/' . $applicationId);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * @throws Throwable
     */
    public function testRegularUserCannotAccessAdminApplicationCrud(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('GET', self::BASE_URL);
        self::assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
