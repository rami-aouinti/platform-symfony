<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel;

use App\Notification\Application\Service\Channel\Interfaces\PushNotificationInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

use function json_encode;
use function sprintf;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
readonly class PushNotification implements PushNotificationInterface
{
    public function __construct(
        private HubInterface $hub,
    ) {
    }

    public function send(string $userId, string $subject, string $content): void
    {
        $payload = json_encode([
            'type' => 'notification',
            'subject' => $subject,
            'content' => $content,
        ]);

        if ($payload === false) {
            return;
        }

        $this->hub->publish(new Update(
            sprintf('/users/%s/notifications', $userId),
            $payload,
        ));
    }
}
