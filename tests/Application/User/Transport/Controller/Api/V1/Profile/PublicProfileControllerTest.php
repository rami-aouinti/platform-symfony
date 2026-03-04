<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class PublicProfileControllerTest extends WebTestCase
{
    private const string BASE_URL = self::API_URL_PREFIX . '/v1/user/%s/profile';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/user/{userId}/profile` is public and returns user profile data.')]
    public function testThatPublicUserProfileEndpointReturnsSuccessResponseForAnonymousUser(): void
    {
        $client = $this->getTestClient();
        $resource = static::getContainer()->get(UserResource::class);
        $userEntity = $resource->findOneBy([
            'username' => 'john-root',
        ]);

        self::assertInstanceOf(User::class, $userEntity);

        $client->request(method: 'GET', uri: sprintf(self::BASE_URL, $userEntity->getId()));
        $response = $client->getResponse();
        $content = $response->getContent();

        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);

        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertSame($userEntity->getId(), $responseData['id']);
        self::assertSame($userEntity->getUsername(), $responseData['username']);
    }

    #[TestDox('Test that `GET /api/v1/user/{userId}/profile` returns not found for unknown user.')]
    public function testThatPublicUserProfileEndpointReturnsNotFoundForUnknownUser(): void
    {
        $client = $this->getTestClient();

        $client->request(method: 'GET', uri: sprintf(self::BASE_URL, '0194dc28-6ab5-11ef-8c77-0242ac120002'));
        $response = $client->getResponse();

        self::assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode(), "Response:\n" . $response);
    }
}
