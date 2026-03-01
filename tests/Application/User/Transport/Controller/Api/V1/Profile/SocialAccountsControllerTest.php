<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SocialAccountsControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/me/social-accounts';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that social account can be linked and listed for logged user only.')]
    public function testThatLinkAndListSocialAccountsWorksForCurrentUser(): void
    {
        $client = $this->getTestClient('john-logged', 'password-logged');

        $client->request(
            method: 'POST',
            uri: $this->baseUrl . '/link',
            content: JSON::encode([
                'provider' => 'github',
                'providerUserId' => 'john-gh-id',
                'providerEmail' => 'john@example.test',
            ]),
        );

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request(method: 'GET', uri: $this->baseUrl);
        $response = $client->getResponse();
        self::assertSame(Response::HTTP_OK, $response->getStatusCode());

        $responseData = JSON::decode((string) $response->getContent(), true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('items', $responseData);
        self::assertIsArray($responseData['items']);

        $providers = array_column($responseData['items'], 'provider');
        self::assertContains('github', $providers);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that social account can be unlinked for logged user.')]
    public function testThatUnlinkSocialAccountWorksForCurrentUser(): void
    {
        $client = $this->getTestClient('john-logged', 'password-logged');

        $client->request(
            method: 'POST',
            uri: $this->baseUrl . '/link',
            content: JSON::encode([
                'provider' => 'gitlab',
                'providerUserId' => 'john-gl-id',
            ]),
        );

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $client->request(method: 'DELETE', uri: $this->baseUrl . '/gitlab');
        self::assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }
}
