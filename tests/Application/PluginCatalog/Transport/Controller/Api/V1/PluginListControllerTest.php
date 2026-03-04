<?php

declare(strict_types=1);

namespace App\Tests\Application\PluginCatalog\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class PluginListControllerTest extends WebTestCase
{
    private const string URL = self::API_URL_PREFIX . '/v1/plugins';

    /**
     * @throws Throwable
     */
    public function testPluginsCatalogIsPublic(): void
    {
        $client = $this->getTestClient();
        $client->request(Request::METHOD_GET, self::URL);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $payload = $this->decodeResponse($client->getResponse()->getContent());
        self::assertArrayHasKey('items', $payload);
        self::assertIsArray($payload['items']);
        self::assertNotEmpty($payload['items']);
        self::assertArrayHasKey('keyName', $payload['items'][0]);
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
}
