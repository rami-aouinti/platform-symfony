<?php

declare(strict_types=1);

namespace App\Tests\Application\Media\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

use function file_exists;
use function file_put_contents;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class MeMediaControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/me/media';

    /**
     * @throws Throwable
     */
    public function testUploadToRootAndFolderRenameAndDeleteFileEndpoints(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $tempFile = tempnam(sys_get_temp_dir(), 'media-upload-');
        self::assertNotFalse($tempFile);
        file_put_contents($tempFile, 'demo-file-content');

        $uploadedFile = new UploadedFile(
            $tempFile,
            'my-document.txt',
            'text/plain',
            null,
            true,
        );

        $client->request('POST', self::BASE_URL . '/files/upload', [], ['file' => $uploadedFile]);

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $rootUploadPayload */
        $rootUploadPayload = JSON::decode((string)$client->getResponse()->getContent(), true);
        $rootMediaId = $rootUploadPayload['id'] ?? null;

        self::assertIsString($rootMediaId);

        $folderUploadFilePath = tempnam(sys_get_temp_dir(), 'media-upload-folder-');
        self::assertNotFalse($folderUploadFilePath);
        file_put_contents($folderUploadFilePath, 'demo-folder-file-content');

        $uploadedFolderFile = new UploadedFile(
            $folderUploadFilePath,
            'in-folder.txt',
            'text/plain',
            null,
            true,
        );

        $client->request(
            'POST',
            self::BASE_URL . '/upload',
            ['folderId' => '80000000-0000-1000-8000-000000000011'],
            ['file' => $uploadedFolderFile],
        );

        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $folderUploadPayload */
        $folderUploadPayload = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame('in-folder.txt', $folderUploadPayload['name'] ?? null);

        $client->request(
            'PATCH',
            self::BASE_URL . '/files/' . $rootMediaId . '/rename',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            JSON::encode(['name' => 'renamed-file.txt']),
        );

        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        /** @var array<string, mixed> $renamePayload */
        $renamePayload = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertSame('renamed-file.txt', $renamePayload['name'] ?? null);

        $client->request('DELETE', self::BASE_URL . '/files/' . $rootMediaId);
        self::assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());

        $client->request('GET', self::BASE_URL . '/' . $rootMediaId);
        self::assertSame(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        if (file_exists($folderUploadFilePath)) {
            unlink($folderUploadFilePath);
        }
    }
}
