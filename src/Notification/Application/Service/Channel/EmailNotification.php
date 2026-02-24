<?php

declare(strict_types=1);

namespace App\Notification\Application\Service\Channel;

use App\General\Domain\Service\Interfaces\MailerServiceInterface;
use App\Notification\Application\Service\Channel\Interfaces\EmailNotificationInterface;

/**
 * @package
 * @author  Rami Aouinti <rami.aouinti@gmail.com>
 */
readonly class EmailNotification implements EmailNotificationInterface
{
    public function __construct(
        private MailerServiceInterface $mailerService,
        private string $senderEmail,
    ) {
    }

    public function send(string $to, string $subject, string $content): void
    {
        $this->mailerService->sendMail($subject, $this->senderEmail, $to, $content);
    }
}
