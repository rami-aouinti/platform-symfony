<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel;

use App\Notification\Application\Service\Channel\Interfaces\SmsNotificationInterface;
use Symfony\Component\Notifier\Message\SmsMessage;

use function is_object;
use function method_exists;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
readonly class SmsNotification implements SmsNotificationInterface
{
    public function __construct(
        private ?object $texter = null,
        private string $fromNumber = '',
    ) {
    }

    public function send(string $phoneNumber, string $content): void
    {
        if (!$this->canSend()) {
            return;
        }

        $message = (new SmsMessage($phoneNumber, $content))->from($this->fromNumber);

        $this->texter->send($message);
    }

    private function canSend(): bool
    {
        return is_object($this->texter) && method_exists($this->texter, 'send');
    }
}
