<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel;

use App\Notification\Application\Service\Channel\Interfaces\PushNotificationInterface;

use function class_exists;
use function is_object;
use function json_encode;
use function method_exists;
use function sprintf;

/**
 * @package App\Notification\Application\Service\Channel* @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
readonly class PushNotification implements PushNotificationInterface
{
    public function __construct(
        private ?object $hub = null,
    ) {
    }

    public function send(string $userId, string $subject, string $content): void
    {
        $payload = json_encode([
            'type' => 'notification',
            'subject' => $subject,
            'content' => $content,
        ]);

        if ($payload === false || !$this->canPublish()) {
            return;
        }

        $updateClass = 'Symfony\\Component\\Mercure\\Update';

        $this->hub->publish(new $updateClass(
            sprintf('/users/%s/notifications', $userId),
            $payload,
        ));
    }

    private function canPublish(): bool
    {
        return is_object($this->hub)
            && class_exists('Symfony\\Component\\Mercure\\Update')
            && method_exists($this->hub, 'publish');
    }
}
