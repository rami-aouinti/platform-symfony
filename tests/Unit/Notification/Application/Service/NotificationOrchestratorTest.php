<?php

declare(strict_types=1);

namespace App\Tests\Unit\Notification\Application\Service;

use App\General\Domain\Service\Interfaces\MessageServiceInterface;
use App\JobApplication\Domain\Enum\JobApplicationStatus;
use App\Notification\Application\Service\Interfaces\NotificationServiceInterface;
use App\Notification\Application\Service\NotificationOrchestrator;
use App\Notification\Domain\Message\NotificationRealtimePublishMessage;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\TestCase;

class NotificationOrchestratorTest extends TestCase
{
    public function testNotifyJobApplicationDecidedCreatesStandardTypeAndDispatchesRealtimeMessage(): void
    {
        $candidate = (new User())
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setUsername('jane.doe')
            ->setEmail('jane@example.com');

        $notificationService = $this->createMock(NotificationServiceInterface::class);
        $notificationService
            ->expects($this->once())
            ->method('create')
            ->with(
                $candidate,
                'job_application_decided',
                'Mise à jour de candidature',
                "Votre candidature app-1 pour l'offre offer-1 a été acceptée.",
            );

        $messageService = $this->createMock(MessageServiceInterface::class);
        $messageService
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(static function (object $message) use ($candidate): bool {
                return $message instanceof NotificationRealtimePublishMessage
                    && $message->userId === $candidate->getId()
                    && $message->title === 'Mise à jour de candidature'
                    && $message->message === "Votre candidature app-1 pour l'offre offer-1 a été acceptée.";
            }));

        $orchestrator = new NotificationOrchestrator($notificationService, $messageService);

        $orchestrator->notifyJobApplicationDecided($candidate, JobApplicationStatus::ACCEPTED, 'app-1', 'offer-1');
    }
}
