<?php

declare(strict_types=1);

namespace App\Tests\Application\Media\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function is_array;

class MediaFolderControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/me/media/folders';

    /**
     * @throws Throwable
     */
    public function testTreeIncludesFoldersAndFilesForJohnRoot(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $client->request('GET', self::BASE_URL . '?tree=true');

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        /** @var array<int, array<string, mixed>> $payload */
        $payload = JSON::decode((string)$client->getResponse()->getContent(), true);

        self::assertCount(1, $payload);

        $root = $payload[0];
        self::assertSame('ROOT', $root['name']);
        self::assertSame('folder', $root['type']);

        $eventsFolder = $this->findNodeByName($root, 'Events', 'folder');
        self::assertNotNull($eventsFolder);

        $eventsFile = $this->findNodeByName($eventsFolder, 'team-event.jpg', 'file');
        self::assertNotNull($eventsFile);
        self::assertSame('media/john-root/images/events/team-event.jpg', $eventsFile['path']);

        $reportsFolder = $this->findNodeByName($root, 'Reports', 'folder');
        self::assertNotNull($reportsFolder);

        $reportFile = $this->findNodeByName($reportsFolder, 'report-q1.pdf', 'file');
        self::assertNotNull($reportFile);
        self::assertSame('application/pdf', $reportFile['mimeType']);
    }


    /**
     * @throws Throwable
     */
    public function testListReturnsChildrenAndFilesWithoutParentIdField(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $client->request('GET', self::BASE_URL);

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        /** @var array<int, array<string, mixed>> $payload */
        $payload = JSON::decode((string)$client->getResponse()->getContent(), true);

        self::assertNotEmpty($payload);

        $images = null;

        foreach ($payload as $folder) {
            if (($folder['name'] ?? null) === 'Images') {
                $images = $folder;
                break;
            }
        }

        self::assertIsArray($images);
        self::assertArrayNotHasKey('parentId', $images);
        self::assertSame('folder', $images['type'] ?? null);
        self::assertIsArray($images['children'] ?? null);

        $avatar = $this->findNodeByName($images, 'avatar-john.png', 'file');
        self::assertNotNull($avatar);
        self::assertArrayHasKey('mimeType', $avatar);
        self::assertArrayHasKey('folderId', $avatar);
    }

    /**
     * @param array<string, mixed> $node
     *
     * @return array<string, mixed>|null
     */
    private function findNodeByName(array $node, string $name, string $type): ?array
    {
        if (($node['name'] ?? null) === $name && ($node['type'] ?? null) === $type) {
            return $node;
        }

        $children = $node['children'] ?? [];

        if (!is_array($children)) {
            return null;
        }

        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            $found = $this->findNodeByName($child, $name, $type);

            if ($found !== null) {
                return $found;
            }
        }

        return null;
    }
}
