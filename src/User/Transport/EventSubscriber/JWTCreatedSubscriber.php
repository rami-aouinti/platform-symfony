<?php

declare(strict_types=1);

namespace App\User\Transport\EventSubscriber;

use App\Chat\Domain\Repository\Interfaces\ConversationParticipantRepositoryInterface;
use App\General\Domain\Enum\Language;
use App\General\Domain\Enum\Locale;
use App\Tool\Domain\Service\Interfaces\LocalizationServiceInterface;
use App\User\Application\Security\SecurityUser;
use DateTime;
use DateTimeZone;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Override;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

use function hash;
use function implode;
use function array_map;
use function sprintf;

/**
 * @package App\User
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly LoggerInterface $logger,
        private readonly ConversationParticipantRepositoryInterface $conversationParticipantRepository,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string>
     */
    #[Override]
    public static function getSubscribedEvents(): array
    {
        return [
            JWTCreatedEvent::class => 'onJWTCreated',
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }

    /**
     * Subscriber method to attach some custom data to current JWT payload.
     *
     * This method is called when following event is broadcast;
     *  - lexik_jwt_authentication.on_jwt_created
     *
     * @throws Exception
     */
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        // Get current original payload
        $payload = $event->getData();
        // Set localization data
        $this->setLocalizationData($payload, $event->getUser());
        // Update JWT expiration data
        $this->setExpiration($payload);
        // Add organization context to payload
        $this->setOrganizationData($payload, $event->getUser());
        // Add some extra security data to payload
        $this->setSecurityData($payload);
        // Add Mercure claims for authorized subscriptions
        $this->setMercureClaims($payload, $event->getUser());
        // And set new payload for JWT
        $event->setData($payload);
    }

    /**
     * @param array<string, string> $payload
     */
    private function setLocalizationData(array &$payload, UserInterface $user): void
    {
        $payload['language'] = $user instanceof SecurityUser
            ? $user->getLanguage()->value
            : Language::getDefault()->value;
        $payload['locale'] = $user instanceof SecurityUser
            ? $user->getLocale()->value
            : Locale::getDefault()->value;
        $payload['timezone'] = $user instanceof SecurityUser
            ? $user->getTimezone()
            : LocalizationServiceInterface::DEFAULT_TIMEZONE;
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function setOrganizationData(array &$payload, UserInterface $user): void
    {
        if (!($user instanceof SecurityUser)) {
            return;
        }

        $payload['organizations'] = $user->getOrganizations();

        $request = $this->requestStack->getCurrentRequest();

        if ($request !== null) {
            $payload['organization_context'] = $request->headers->get('X-Company-Id');
        }
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function setMercureClaims(array &$payload, UserInterface $user): void
    {
        if (!($user instanceof SecurityUser)) {
            return;
        }

        $userId = $user->getUserIdentifier();
        $conversationTopics = $this->conversationParticipantRepository->findConversationIdsByUserId($userId);

        $subscribe = [
            sprintf('/users/%s/notifications', $userId),
            ...array_map(
                static fn (string $conversationId): string => sprintf('/conversations/%s', $conversationId),
                $conversationTopics,
            ),
        ];

        $payload['mercure'] = [
            'subscribe' => $subscribe,
        ];
    }

    /**
     * Method to set/modify JWT expiration date dynamically.
     *
     * @param array<string, string|int> $payload
     *
     * @throws Exception
     */
    private function setExpiration(array &$payload): void
    {
        // Set new exp value for JWT
        $payload['exp'] = new DateTime('+1 day', new DateTimeZone('UTC'))->getTimestamp();
    }

    /**
     * Method to add some security related data to JWT payload, which are checked on JWT decode process.
     *
     * @see JWTDecodedListener
     *
     * @param array<string, string|int> $payload
     */
    private function setSecurityData(array &$payload): void
    {
        // Get current request
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            $this->logger->alert('Request not available');

            return;
        }

        // Get bits for checksum calculation
        $bits = [
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
        ];
        // Attach checksum to JWT payload
        $payload['checksum'] = hash('sha512', implode('|', $bits));
    }
}
