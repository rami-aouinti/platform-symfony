<?php

declare(strict_types=1);

namespace App\Tests\Application\Resume\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResumeControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/resumes';
    private const string BOB_PUBLIC_RESUME_ID = '60000000-0000-1000-8000-000000000001';
    private const string BOB_PRIVATE_RESUME_ID = '60000000-0000-1000-8000-000000000002';

    /** @throws Throwable */
    public function testOwnerCanCreateShowEditAndDeleteResume(): void
    {
        $ownerClient = $this->getTestClient('bob-admin', 'password-admin');

        $payload = [
            'title' => 'Bob Application Resume',
            'summary' => 'Resume generated in integration test.',
            'experiences' => [['company' => 'ACME', 'role' => 'Backend Engineer']],
            'education' => [['school' => 'Tech University', 'degree' => 'MSc']],
            'skills' => ['PHP', 'Symfony'],
            'links' => [['label' => 'GitHub', 'url' => 'https://example.test/bob-github']],
            'isPublic' => false,
        ];

        $ownerClient->request('POST', self::BASE_URL, content: JSON::encode($payload));
        self::assertSame(Response::HTTP_CREATED, $ownerClient->getResponse()->getStatusCode());

        $createdPayload = JSON::decode((string) $ownerClient->getResponse()->getContent(), true);
        $resumeId = (string) $createdPayload['id'];
        self::assertSame($payload['title'], $createdPayload['title']);

        $ownerClient->request('GET', self::BASE_URL . '/' . $resumeId);
        self::assertSame(Response::HTTP_OK, $ownerClient->getResponse()->getStatusCode());

        $ownerClient->request('PATCH', self::BASE_URL . '/' . $resumeId, content: JSON::encode([
            'title' => 'Bob Application Resume Updated',
            'isPublic' => true,
        ]));
        self::assertSame(Response::HTTP_OK, $ownerClient->getResponse()->getStatusCode());

        $patchedPayload = JSON::decode((string) $ownerClient->getResponse()->getContent(), true);
        self::assertSame('Bob Application Resume Updated', $patchedPayload['title']);
        self::assertTrue($patchedPayload['isPublic']);

        $ownerClient->request('DELETE', self::BASE_URL . '/' . $resumeId);
        self::assertSame(Response::HTTP_OK, $ownerClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testOtherUserCannotEditOrDeleteResume(): void
    {
        $forbiddenClient = $this->getTestClient('carol-user', 'password-user');

        $forbiddenClient->request('PATCH', self::BASE_URL . '/' . self::BOB_PRIVATE_RESUME_ID, content: JSON::encode([
            'title' => 'Should fail',
        ]));
        self::assertSame(Response::HTTP_FORBIDDEN, $forbiddenClient->getResponse()->getStatusCode());

        $forbiddenClient->request('DELETE', self::BASE_URL . '/' . self::BOB_PRIVATE_RESUME_ID);
        self::assertSame(Response::HTTP_FORBIDDEN, $forbiddenClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testNotFoundForMissingOrInaccessibleResume(): void
    {
        $ownerClient = $this->getTestClient('bob-admin', 'password-admin');
        $ownerClient->request('GET', self::BASE_URL . '/60000000-0000-1000-8000-000000000099');
        self::assertSame(Response::HTTP_NOT_FOUND, $ownerClient->getResponse()->getStatusCode());

        $outsiderClient = $this->getTestClient('john-user', 'password-user');
        $outsiderClient->request('GET', self::BASE_URL . '/' . self::BOB_PRIVATE_RESUME_ID);
        self::assertSame(Response::HTTP_NOT_FOUND, $outsiderClient->getResponse()->getStatusCode());
    }

    /** @throws Throwable */
    public function testPublicResumeIsReadableByAnotherUser(): void
    {
        $outsiderClient = $this->getTestClient('john-user', 'password-user');
        $outsiderClient->request('GET', self::BASE_URL . '/' . self::BOB_PUBLIC_RESUME_ID);

        self::assertSame(Response::HTTP_OK, $outsiderClient->getResponse()->getStatusCode());
        $payload = JSON::decode((string) $outsiderClient->getResponse()->getContent(), true);
        self::assertSame(self::BOB_PUBLIC_RESUME_ID, $payload['id']);
    }
}
