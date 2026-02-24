<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel;

use App\Notification\Application\Service\Channel\Interfaces\SmsNotificationInterface;
use Symfony\Component\Notifier\Bridge\Twilio\Texter\TwilioTexter;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;

readonly class SmsNotification implements SmsNotificationInterface
{
    public function __construct(
        private TexterInterface $texter,
        private string $fromNumber,
    ) {
    }

    public function send(string $phoneNumber, string $content): void
    {
        $message = (new SmsMessage($phoneNumber, $content))->from($this->fromNumber);

        $this->texter->send($message);
    }
}
