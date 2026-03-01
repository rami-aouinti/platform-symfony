<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Role\Application\Security\RolesService;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class IndexControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/profile';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/profile` for the `john-root` user returns success response.')]
    public function testThatGetUserProfileActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');
        $roleService = static::getContainer()->get(RolesService::class);
        $resource = static::getContainer()->get(UserResource::class);
        $userEntity = $resource->findOneBy([
            'username' => 'john-root',
        ]);
        self::assertInstanceOf(User::class, $userEntity);

        $client->request(method: 'GET', uri: $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('id', $responseData);
        self::assertEquals($userEntity->getId(), $responseData['id']);
        self::assertArrayHasKey('username', $responseData);
        self::assertEquals($userEntity->getUsername(), $responseData['username']);
        self::assertArrayHasKey('firstName', $responseData);
        self::assertEquals($userEntity->getFirstName(), $responseData['firstName']);
        self::assertArrayHasKey('lastName', $responseData);
        self::assertEquals($userEntity->getLastName(), $responseData['lastName']);
        self::assertArrayHasKey('email', $responseData);
        self::assertEquals($userEntity->getEmail(), $responseData['email']);
        self::assertArrayHasKey('language', $responseData);
        self::assertEquals($userEntity->getLanguage()->value, $responseData['language']);
        self::assertArrayHasKey('locale', $responseData);
        self::assertEquals($userEntity->getLocale()->value, $responseData['locale']);
        self::assertArrayHasKey('timezone', $responseData);
        self::assertEquals($userEntity->getTimezone(), $responseData['timezone']);
        self::assertArrayHasKey('roles', $responseData);
        self::assertIsArray($responseData['roles']);
        self::assertCount(count($roleService->getInheritedRoles($userEntity->getRoles())), $responseData['roles']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/profile` for non-logged user returns error response.')]
    public function testThatGetGetUserProfileActionForNonLoggedUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient();

        $client->request(method: 'GET', uri: $this->baseUrl);
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PATCH /api/v1/profile` updates currently logged in user profile data.')]
    public function testThatPatchProfileActionUpdatesLoggedInUserProfile(): void
    {
        $client = $this->getTestClient('john-logged', 'password-logged');

        $client->request(
            method: 'PATCH',
            uri: $this->baseUrl,
            content: JSON::encode([
                'firstName' => 'John Updated',
                'lastName' => 'Logged Updated',
                'userProfile' => [
                    'phone' => '+33123456789',
                    'bio' => 'Updated bio',
                ],
            ]),
        );

        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);

        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertSame('John Updated', $responseData['firstName']);
        self::assertSame('Logged Updated', $responseData['lastName']);
        self::assertArrayHasKey('userProfile', $responseData);
        self::assertIsArray($responseData['userProfile']);
        self::assertSame('+33123456789', $responseData['userProfile']['phone']);
        self::assertSame('Updated bio', $responseData['userProfile']['bio']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PUT /api/v1/profile/avatar` updates currently logged in user avatar.')]
    public function testThatUpdateAvatarActionUpdatesLoggedInUserAvatar(): void
    {
        $client = $this->getTestClient('john-logged', 'password-logged');

        $client->request(
            method: 'PUT',
            uri: $this->baseUrl . '/avatar',
            content: JSON::encode([
                'mediaId' => 'avatar-123',
                'url' => 'https://cdn.example.com/avatar.png',
            ]),
        );

        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);

        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('userProfile', $responseData);
        self::assertIsArray($responseData['userProfile']);
        self::assertArrayHasKey('avatar', $responseData['userProfile']);
        self::assertIsArray($responseData['userProfile']['avatar']);
        self::assertSame('avatar-123', $responseData['userProfile']['avatar']['mediaId']);
        self::assertSame('https://cdn.example.com/avatar.png', $responseData['userProfile']['avatar']['url']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `POST /api/v1/profile/address` adds address to currently logged in user profile.')]
    public function testThatAddAddressActionAddsAddressToLoggedInUserProfile(): void
    {
        $client = $this->getTestClient('john-logged', 'password-logged');

        $client->request(
            method: 'POST',
            uri: $this->baseUrl . '/address',
            content: JSON::encode([
                'type' => 'home',
                'streetLine1' => '1 Symfony Street',
                'postalCode' => '75001',
                'city' => 'Paris',
                'countryCode' => 'FR',
                'region' => 'Ile-de-France',
            ]),
        );

        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);

        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertArrayHasKey('userProfile', $responseData);
        self::assertIsArray($responseData['userProfile']);
        self::assertArrayHasKey('addresses', $responseData['userProfile']);
        self::assertIsArray($responseData['userProfile']['addresses']);
        self::assertNotEmpty($responseData['userProfile']['addresses']);

        /** @var array<string, mixed> $address */
        $address = $responseData['userProfile']['addresses'][array_key_last($responseData['userProfile']['addresses'])];

        self::assertSame('home', $address['type']);
        self::assertSame('1 Symfony Street', $address['streetLine1']);
        self::assertSame('75001', $address['postalCode']);
        self::assertSame('Paris', $address['city']);
        self::assertSame('FR', $address['countryCode']);
        self::assertSame('Ile-de-France', $address['region']);
    }
}
