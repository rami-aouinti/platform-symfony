<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\Configuration\Domain\Entity\Configuration;
use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use App\User\Domain\Repository\Interfaces\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ConfigurationsControllerTest extends WebTestCase
{
    private const string ENDPOINT = self::API_URL_PREFIX . '/v1/me/profile/configurations';

    /**
     * @throws Throwable
     */
    public function testAuthenticatedUserCanGetOwnProfileConfigurationsWithKeyNameFilter(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var UserRepositoryInterface $userRepository */
        $userRepository = $container->get(UserRepositoryInterface::class);

        $user = $userRepository->loadUserByIdentifier('john-user', true);
        self::assertNotNull($user);

        $profile = $user->getOrCreateUserProfile();

        $configA = (new Configuration())
            ->setCode('me-cfg-a-' . uniqid())
            ->setKeyName('profile.notifications.email.' . uniqid())
            ->setValue(['enabled' => true])
            ->setStatus('active')
            ->setProfile($profile);

        $configB = (new Configuration())
            ->setCode('me-cfg-b-' . uniqid())
            ->setKeyName('profile.theme.' . uniqid())
            ->setValue(['theme' => 'dark'])
            ->setStatus('active')
            ->setProfile($profile);

        $em->persist($configA);
        $em->persist($configB);
        $em->flush();

        $client->request('GET', self::ENDPOINT);
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $list = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($list);

        $matching = array_values(array_filter(
            $list,
            static fn (mixed $item): bool => is_array($item)
                && (($item['id'] ?? null) === $configA->getId() || ($item['id'] ?? null) === $configB->getId()),
        ));
        self::assertCount(2, $matching);

        $client->request('GET', self::ENDPOINT . '?keyName=notifications');
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $filtered = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($filtered);
        self::assertCount(1, $filtered);
        self::assertSame($configA->getId(), $filtered[0]['id'] ?? null);
        self::assertSame($configA->getKeyName(), $filtered[0]['keyName'] ?? null);

        $em->remove($configA);
        $em->remove($configB);
        $em->flush();
    }


    /**
     * @throws Throwable
     */
    public function testEndpointReturnsNotificationFixtureForCurrentProfile(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');

        $client->request('GET', self::ENDPOINT . '?keyName=notification');
        self::assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $data = JSON::decode((string)$client->getResponse()->getContent(), true);
        self::assertIsArray($data);
        self::assertNotEmpty($data);

        $notificationConfig = array_values(array_filter(
            $data,
            static fn (mixed $item): bool => is_array($item)
                && ($item['keyName'] ?? null) === 'notification',
        ));

        self::assertCount(1, $notificationConfig);
        self::assertCount(4, $notificationConfig[0]['value'] ?? []);
        self::assertSame('mentions', $notificationConfig[0]['value'][0]['key'] ?? null);
    }

    /**
     * @throws Throwable
     */
    public function testUnauthenticatedUserCannotAccessProfileConfigurations(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', self::ENDPOINT);

        self::assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}
